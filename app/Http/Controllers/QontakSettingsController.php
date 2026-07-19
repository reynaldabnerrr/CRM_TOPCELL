<?php

namespace App\Http\Controllers;

use App\Models\QontakSetting;
use App\Services\QontakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QontakSettingsController extends Controller
{
    /**
     * Show the form for editing Qontak settings.
     */
    public function edit()
    {
        $settings = QontakSetting::getSettings();
        $templates = [];

        if (!empty($settings->access_token)) {
            try {
                $qontakService = new QontakService();
                $templates = $qontakService->getTemplates() ?: [];
            } catch (\Exception $e) {
                Log::error('Gagal mengambil template Qontak: ' . $e->getMessage());
            }
        }

        return view('qontak-settings.edit', compact('settings', 'templates'));
    }

    /**
     * Update the Qontak settings in database.
     */
    public function update(Request $request)
    {
        $request->validate([
            'base_url'                     => 'required|url',
            'access_token'                 => 'nullable|string',
            'chatbot_token'                => 'nullable|string',
            'refresh_token'                => 'nullable|string',
            'client_id'                    => 'nullable|string',
            'client_secret'                => 'nullable|string',
            'channel_integration_id'       => 'nullable|string',
            'sales_template_h1'            => 'nullable|string',
            'sales_template_h1_vars'       => 'nullable|integer|min:1|max:3',
            'sales_template_h7'            => 'nullable|string',
            'sales_template_h7_vars'       => 'nullable|integer|min:1|max:3',
            'sales_template_1month'        => 'nullable|string',
            'sales_template_1month_vars'   => 'nullable|integer|min:1|max:3',
            'pending_template_h1'          => 'nullable|string',
            'pending_template_h1_vars'     => 'nullable|integer|min:1|max:3',
            'pending_template_h7'          => 'nullable|string',
            'pending_template_h7_vars'     => 'nullable|integer|min:1|max:3',
            'pending_template_1month'      => 'nullable|string',
            'pending_template_1month_vars' => 'nullable|integer|min:1|max:3',
            'variable_mappings'            => 'nullable|array',
        ]);

        $settings = QontakSetting::getSettings();
        
        // Fill basic credentials and template UUIDs first
        $settings->fill($request->only([
            'base_url',
            'access_token',
            'chatbot_token',
            'refresh_token',
            'client_id',
            'client_secret',
            'channel_integration_id',
            'sales_template_h1',
            'sales_template_h7',
            'sales_template_1month',
            'pending_template_h1',
            'pending_template_h7',
            'pending_template_1month',
        ]));
        
        // Save base credentials first, so QontakService gets the updated tokens/url
        $settings->save();

        // Fetch templates list from API using QontakService to auto-detect variable counts
        $templatesList = [];
        if (!empty($settings->access_token)) {
            try {
                $qontakService = new QontakService();
                $templatesList = $qontakService->getTemplates() ?: [];
            } catch (\Exception $e) {
                Log::error('Auto variables detection failed to fetch templates: ' . $e->getMessage());
            }
        }

        $templateFields = [
            'sales_template_h1' => 'sales_template_h1_vars',
            'sales_template_h7' => 'sales_template_h7_vars',
            'sales_template_1month' => 'sales_template_1month_vars',
            'pending_template_h1' => 'pending_template_h1_vars',
            'pending_template_h7' => 'pending_template_h7_vars',
            'pending_template_1month' => 'pending_template_1month_vars',
        ];

        $variableMappings = $request->input('variable_mappings') ?: ($settings->variable_mappings ?: []);

        // Preserve body text if already exists and not overridden
        foreach ($templateFields as $field => $varsField) {
            if (isset($settings->variable_mappings[$field]['body']) && !isset($variableMappings[$field]['body'])) {
                if (!isset($variableMappings[$field])) {
                    $variableMappings[$field] = [];
                }
                $variableMappings[$field]['body'] = $settings->variable_mappings[$field]['body'];
            }
        }

        foreach ($templateFields as $field => $varsField) {
            $uuid = $request->input($field);
            $foundInApi = false;

            if (!empty($uuid) && !empty($templatesList)) {
                foreach ($templatesList as $tmpl) {
                    if (($tmpl['id'] ?? '') === $uuid) {
                        $body = $tmpl['body'] ?? '';
                        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);
                        $count = !empty($matches[1]) ? (int) max($matches[1]) : 0;
                        
                        $settings->$varsField = $count;

                        if (!isset($variableMappings[$field])) {
                            $variableMappings[$field] = [];
                        }
                        $variableMappings[$field]['body'] = $body;

                        $foundInApi = true;
                        break;
                    }
                }
            }

            // Fallback: If not found in Qontak templates API list, use user's manual dropdown input or default to original value / standard default
            if (!$foundInApi) {
                if ($request->has($varsField) && !is_null($request->input($varsField))) {
                    $settings->$varsField = $request->input($varsField);
                } elseif (is_null($settings->$varsField)) {
                    $settings->$varsField = str_starts_with($field, 'sales') ? 3 : 2;
                }
            }
        }

        $settings->variable_mappings = $variableMappings;
        $settings->save();

        return redirect()
            ->route('qontak-settings.edit')
            ->with('success', 'Konfigurasi Qontak berhasil disimpan. Jumlah variabel terdeteksi otomatis.');
    }

    /**
     * Test Qontak token refresh mechanism manually.
     */
    public function testRefresh()
    {
        $qontakService = new QontakService();
        $result = $qontakService->refreshToken();

        if ($result['success']) {
            return redirect()
                ->route('qontak-settings.edit')
                ->with('success', 'Token Qontak berhasil diperbarui secara manual!');
        }

        return redirect()
            ->route('qontak-settings.edit')
            ->with('error', 'Gagal memperbarui token Qontak: ' . ($result['error'] ?? 'Terjadi kesalahan.'));
    }

    /**
     * Show approved WhatsApp templates fetched from Mekari Qontak API.
     */
    public function showTemplates()
    {
        $settings = QontakSetting::getSettings();
        $accessToken = $settings->access_token;

        if (empty($accessToken)) {
            return redirect()
                ->route('qontak-settings.edit')
                ->with('error', 'Access Token kosong. Silakan konfigurasikan terlebih dahulu.');
        }

        $url = rtrim($settings->base_url, '/') . '/api/open/v1/templates/whatsapp';
        
        try {
            Log::info('Fetching templates list from Qontak API: ' . $url);

            $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->timeout(15)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $templates = $data['data'] ?? [];
                
                Log::info('Successfully loaded templates count: ' . count($templates));

                return view('qontak-settings.templates', compact('templates'));
            }

            // Retry once if token refresh helps (in case it is expired)
            if ($response->status() === 401) {
                Log::warning('Unauthorized while fetching templates. Attempting auto-refresh token...');
                $qontakService = new QontakService();
                $refreshResult = $qontakService->refreshToken();
                
                if ($refreshResult['success']) {
                    Log::info('Token refresh succeeded. Retrying template fetch...');
                    // Re-fetch token from updated db settings
                    $newSettings = QontakSetting::getSettings();
                    $response = \Illuminate\Support\Facades\Http::withToken($newSettings->access_token)
                        ->timeout(15)
                        ->get($url);

                    if ($response->successful()) {
                        $data = $response->json();
                        $templates = $data['data'] ?? [];
                        return view('qontak-settings.templates', compact('templates'));
                    }
                }
            }

            $errorMsg = $response->json()['message'] ?? "HTTP error {$response->status()}";
            return redirect()
                ->route('qontak-settings.edit')
                ->with('error', 'Gagal memuat template dari Qontak API: ' . $errorMsg);

        } catch (\Exception $e) {
            Log::error('Exception fetching Qontak templates: ' . $e->getMessage());
            return redirect()
                ->route('qontak-settings.edit')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Dynamically register/update the Webhook URL in Qontak using the saved credentials.
     */
    public function registerWebhook()
    {
        $settings = QontakSetting::getSettings();
        
        if (empty($settings->access_token)) {
            return redirect()
                ->route('qontak-settings.edit')
                ->with('error', 'Access Token Qontak kosong. Silakan isi terlebih dahulu.');
        }

        // Detect dynamic webhook endpoint URL
        $webhookUrl = route('qontak.webhook');
        $baseUrl = rtrim($settings->base_url, '/');
        
        // Dynamically choose endpoint path
        if (str_contains($baseUrl, 'api.mekari.com')) {
            $url = $baseUrl . '/qontak/chat/v1/message_interactions';
        } else {
            $url = $baseUrl . '/api/open/v1/message_interactions';
        }

        Log::info("Webhook Registration: Attempting to register {$webhookUrl} via Qontak API");

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($settings->access_token)
                ->timeout(20)
                ->put($url, [
                    'url' => $webhookUrl,
                    'receive_message_from_customer' => true,
                    'receive_message_from_agent' => true,
                    'status_message' => true,
                    'broadcast_log_status' => true
                ]);

            Log::info("Webhook Registration: Qontak API Response Status: " . $response->status());

            if ($response->successful()) {
                return redirect()
                    ->route('qontak-settings.edit')
                    ->with('success', 'Webhook Qontak berhasil didaftarkan: ' . $webhookUrl);
            }

            // Retry once if token refresh helps (in case it is expired)
            if ($response->status() === 401) {
                Log::warning('Unauthorized while registering webhook. Attempting token refresh...');
                $qontakService = new \App\Services\QontakService();
                $refreshResult = $qontakService->refreshToken();
                
                if ($refreshResult['success']) {
                    Log::info('Token refresh succeeded. Retrying webhook registration...');
                    $newSettings = QontakSetting::getSettings();
                    
                    $response = \Illuminate\Support\Facades\Http::withToken($newSettings->access_token)
                        ->timeout(20)
                        ->put($url, [
                            'url' => $webhookUrl,
                            'receive_message_from_customer' => true,
                            'receive_message_from_agent' => true,
                            'status_message' => true,
                            'broadcast_log_status' => true
                        ]);

                    if ($response->successful()) {
                        return redirect()
                            ->route('qontak-settings.edit')
                            ->with('success', 'Webhook Qontak berhasil didaftarkan setelah refresh token: ' . $webhookUrl);
                    }
                }
            }

            $errorData = $response->json();
            $errorMsg = $errorData['error']['messages'][0] 
                ?? $errorData['message'] 
                ?? "HTTP Error {$response->status()}";

            return redirect()
                ->route('qontak-settings.edit')
                ->with('error', 'Gagal mendaftarkan webhook di Qontak: ' . $errorMsg);

        } catch (\Exception $e) {
            Log::error('Exception registering Qontak webhook: ' . $e->getMessage());
            return redirect()
                ->route('qontak-settings.edit')
                ->with('error', 'Terjadi kesalahan saat mendaftarkan webhook: ' . $e->getMessage());
        }
    }
}
