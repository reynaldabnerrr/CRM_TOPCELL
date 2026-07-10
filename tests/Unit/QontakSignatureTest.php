<?php

namespace Tests\Unit;

use App\Models\QontakSetting;
use App\Services\QontakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QontakSignatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test phone number formatting helper
     */
    public function test_phone_number_formatting(): void
    {
        $service = new QontakService();
        
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('081234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('+6281234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber('6281234567890'));
        $this->assertEquals('6281234567890', $service->formatPhoneNumber(' 0812-3456-7890 '));
    }

    /**
     * Test that token refresh mechanism updates the database record correctly
     */
    public function test_token_refresh_updates_database(): void
    {
        // Setup initial record in database
        $settings = QontakSetting::create([
            'base_url' => 'https://service-chat.qontak.com',
            'access_token' => 'old_access_token',
            'refresh_token' => 'old_refresh_token',
            'channel_integration_id' => 'channel_123',
        ]);

        // Mock HTTP response for Qontak OAuth Refresh Token endpoint
        Http::fake([
            'https://service-chat.qontak.com/oauth/token' => Http::response([
                'access_token' => 'new_access_token',
                'refresh_token' => 'new_refresh_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200)
        ]);

        $service = new QontakService();
        $result = $service->refreshToken();

        // Assert method was successful
        $this->assertTrue($result['success']);
        $this->assertNull($result['error']);

        // Assert database was updated
        $updatedSettings = QontakSetting::first();
        $this->assertEquals('new_access_token', $updatedSettings->access_token);
        $this->assertEquals('new_refresh_token', $updatedSettings->refresh_token);
    }
}
