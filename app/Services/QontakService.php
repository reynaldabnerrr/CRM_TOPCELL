<?php

namespace App\Services;

use App\Models\QontakSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QontakService
{
    // Static client credentials provided by Qontak for integration module
    const CLIENT_ID = 'RRrn6uIxalR_QaHFlcKOqbjHMG63elEdPTair9B9YdY';
    const CLIENT_SECRET = 'Sa8IGIh_HpVK1ZLAF0iFf7jU760osaUNV659pBIZR00';

    /**
     * Get settings dynamically from database
     */
    protected function getSettings(): QontakSetting
    {
        return QontakSetting::getSettings();
    }

    /**
     * Send direct WhatsApp message template to a recipient using Bearer authentication
     *
     * @param string $toNumber Recipient's phone number
     * @param string $toName Recipient's name
     * @param string $templateId WhatsApp template ID (UUID)
     * @param array $bodyParams Key-value variables list, e.g. [ ['key' => '1', 'value' => 'name', 'value_text' => 'John'] ]
     * @param bool $isRetry Internal flag to avoid infinite recursion
     * @return array [ 'success' => bool, 'response' => array|string|null, 'error' => string|null ]
     */
    public function sendWhatsappDirect(string $toNumber, string $toName, string $templateId, array $bodyParams = [], bool $isRetry = false): array
    {
        $settings = $this->getSettings();
        
        $baseUrl = rtrim($settings->base_url, '/');
        $url = $baseUrl . '/api/open/v1/broadcasts/whatsapp/direct';
        $accessToken = $settings->access_token;
        $channelId = $settings->channel_integration_id;

        if (empty($accessToken)) {
            return [
                'success'  => false,
                'response' => null,
                'error'    => 'Access Token Qontak kosong. Silakan konfigurasikan di halaman Pengaturan Qontak.',
            ];
        }

        if (empty($channelId)) {
            return [
                'success'  => false,
                'response' => null,
                'error'    => 'Channel Integration ID Qontak kosong. Silakan konfigurasikan di halaman Pengaturan Qontak.',
            ];
        }

        $formattedNumber = $this->formatPhoneNumber($toNumber);
        
        $payload = [
            'to_name'                => $toName,
            'to_number'              => $formattedNumber,
            'message_template_id'    => $templateId,
            'channel_integration_id' => $channelId,
            'language'               => [
                'code' => 'id',
            ],
            'parameters'             => [
                'body' => $bodyParams,
            ],
        ];

        try {
            Log::info('Sending WhatsApp template via Qontak:', [
                'url'     => $url,
                'to'      => $formattedNumber,
                'template'=> $templateId,
            ]);

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'Connection' => 'close',
                ])
                ->timeout(30)
                ->post($url, $payload);

            Log::info('Qontak API Response Status: ' . $response->status());

            // Handle token expiration (401 Unauthorized)
            if ($response->status() === 401 && !$isRetry) {
                Log::warning('Qontak API unauthorized (401). Attempting to auto-refresh token...');
                
                $refreshResult = $this->refreshToken();
                
                if ($refreshResult['success']) {
                    Log::info('Token refresh succeeded. Retrying WhatsApp broadcast sending...');
                    // Retry once with the new token
                    return $this->sendWhatsappDirect($toNumber, $toName, $templateId, $bodyParams, true);
                } else {
                    Log::error('Token auto-refresh failed: ' . $refreshResult['error']);
                    return [
                        'success'  => false,
                        'response' => null,
                        'error'    => 'Sesi token kedaluwarsa dan gagal diperbarui otomatis: ' . $refreshResult['error'],
                    ];
                }
            }

            if ($response->successful()) {
                return [
                    'success'  => true,
                    'response' => $response->json(),
                    'error'    => null,
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['error']['messages'][0] 
                ?? $errorData['message'] 
                ?? "API HTTP error: {$response->status()}";

            return [
                'success'  => false,
                'response' => $errorData,
                'error'    => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('Exception sending Qontak WA message: ' . $e->getMessage());

            return [
                'success'  => false,
                'response' => null,
                'error'    => $e->getMessage(),
            ];
        }
    }

    /**
     * Get WhatsApp templates from Qontak API
     *
     * @param bool $isRetry Internal flag to avoid infinite recursion on token refresh
     * @return array|null Array of formatted templates, or null/empty array on failure
     */
    public function getTemplates(bool $isRetry = false): ?array
    {
        $settings = $this->getSettings();
        
        $baseUrl = rtrim($settings->base_url, '/');
        $url = $baseUrl . '/api/open/v1/templates/whatsapp';
        $accessToken = $settings->access_token;

        if (empty($accessToken)) {
            Log::warning('Gagal mengambil template Qontak: Access Token kosong.');
            return null;
        }

        try {
            Log::info('Fetching WhatsApp templates from Qontak: ' . $url);

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'Connection' => 'close',
                ])
                ->timeout(30)
                ->get($url, [
                    'limit' => 100,
                ]);

            Log::info('Qontak Templates API Response Status: ' . $response->status());

            // Handle token expiration (401 Unauthorized)
            if ($response->status() === 401 && !$isRetry) {
                Log::warning('Qontak Templates API unauthorized (401). Attempting to auto-refresh token...');
                
                $refreshResult = $this->refreshToken();
                
                if ($refreshResult['success']) {
                    Log::info('Token refresh succeeded. Retrying templates fetch...');
                    return $this->getTemplates(true);
                } else {
                    Log::error('Token auto-refresh failed during templates fetch: ' . $refreshResult['error']);
                    return null;
                }
            }

            if ($response->successful()) {
                $body = $response->json();
                $rawTemplates = $body['data'] ?? [];
                
                $formattedTemplates = [];
                foreach ($rawTemplates as $tmpl) {
                    $templateId = $tmpl['id'] ?? null;
                    if (!$templateId) {
                        continue;
                    }

                    // Extract body text: first check top-level body or message, then components
                    $bodyText = $tmpl['body'] ?? $tmpl['message'] ?? '';
                    if (empty($bodyText)) {
                        $components = $tmpl['components'] ?? [];
                        foreach ($components as $comp) {
                            $type = strtoupper($comp['type'] ?? '');
                            if ($type === 'BODY') {
                                $bodyText = $comp['text'] ?? $comp['body'] ?? '';
                                break;
                            }
                        }
                    }


                    $formattedTemplates[] = [
                        'id'       => $templateId,
                        'name'     => $tmpl['name'] ?? '',
                        'category' => $tmpl['category'] ?? '',
                        'body'     => $bodyText,
                    ];
                }

                return $formattedTemplates;
            }

            Log::error('Qontak Templates API error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Exception getting Qontak templates: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Refresh Access Token using Qontak Refresh Token
     *
     * @return array [ 'success' => bool, 'error' => string|null ]
     */
    public function refreshToken(): array
    {
        $settings = $this->getSettings();
        $baseUrl = rtrim($settings->base_url, '/');
        $url = $baseUrl . '/oauth/token';
        $refreshToken = $settings->refresh_token;

        if (empty($refreshToken)) {
            return [
                'success' => false,
                'error'   => 'Refresh Token kosong di database.',
            ];
        }

        try {
            Log::info('Refreshing Qontak Access Token...');

            $clientId = $settings->client_id ?: self::CLIENT_ID;
            $clientSecret = $settings->client_secret ?: self::CLIENT_SECRET;

            $response = Http::timeout(15)->post($url, [
                'refresh_token' => $refreshToken,
                'grant_type'    => 'refresh_token',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ]);

            Log::info('Qontak Token Refresh Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                
                $newAccessToken = $data['access_token'] ?? null;
                $newRefreshToken = $data['refresh_token'] ?? null;

                if ($newAccessToken && $newRefreshToken) {
                    $settings->update([
                        'access_token'  => $newAccessToken,
                        'refresh_token' => $newRefreshToken,
                    ]);

                    Log::info('Qontak tokens successfully updated in database.');

                    return [
                        'success' => true,
                        'error'   => null,
                    ];
                }
                
                return [
                    'success' => false,
                    'error'   => 'Respons refresh token tidak lengkap.',
                ];
            }

            $errorMsg = $response->json()['message'] 
                ?? $response->json()['error_description'] 
                ?? "HTTP error {$response->status()}";

            return [
                'success' => false,
                'error'   => $errorMsg,
            ];

        } catch (\Exception $e) {
            Log::error('Exception refreshing Qontak token: ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp text message to a customer within active session window (reply)
     *
     * @param string $roomId WhatsApp room ID (obtained from Webhook)
     * @param string $text Message content
     * @param bool $isRetry Internal flag to avoid infinite recursion
     * @return array [ 'success' => bool, 'response' => array|string|null, 'error' => string|null ]
     */
    public function sendWhatsappReply(string $roomId, string $text, bool $isRetry = false): array
    {
        $settings = $this->getSettings();
        
        $baseUrl = rtrim($settings->base_url, '/');
        $url = $baseUrl . '/api/open/v1/messages/whatsapp/bot';
        $accessToken = $settings->chatbot_token ?: $settings->access_token;

        if (empty($accessToken)) {
            return [
                'success'  => false,
                'response' => null,
                'error'    => 'Access Token Qontak kosong. Silakan konfigurasikan di halaman Pengaturan Qontak.',
            ];
        }

        $payload = [
            'room_id' => $roomId,
            'type'    => 'text',
            'text'    => $text,
        ];

        try {
            Log::info('Replying WhatsApp via Qontak:', [
                'url'     => $url,
                'room_id' => $roomId,
            ]);

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'Connection' => 'close',
                ])
                ->timeout(30)
                ->post($url, $payload);

            Log::info('Qontak Reply API Response Status: ' . $response->status());

            // Handle token expiration (401 Unauthorized)
            if ($response->status() === 401 && !$isRetry) {
                Log::warning('Qontak Reply API unauthorized (401). Attempting to auto-refresh token...');
                
                $refreshResult = $this->refreshToken();
                
                if ($refreshResult['success']) {
                    Log::info('Token refresh succeeded. Retrying WhatsApp reply...');
                    return $this->sendWhatsappReply($roomId, $text, true);
                }
            }

            if ($response->successful()) {
                return [
                    'success'  => true,
                    'response' => $response->json(),
                    'error'    => null,
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['error']['messages'][0] 
                ?? $errorData['message'] 
                ?? "API HTTP error: {$response->status()}";

            return [
                'success'  => false,
                'response' => $errorData,
                'error'    => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('Exception replying Qontak WA message: ' . $e->getMessage());

            return [
                'success'  => false,
                'response' => null,
                'error'    => $e->getMessage(),
            ];
        }
    }

    /**
     * Format Indonesian or general phone numbers to international numeric format (e.g. 628123...)
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Strip non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle leading 0
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Default to prepend 62 if not already starting with it or another country code
        if (!str_starts_with($phone, '62') && strlen($phone) >= 9 && strlen($phone) <= 15) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
