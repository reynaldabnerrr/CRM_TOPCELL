<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QontakSetting extends Model
{
    use HasFactory;

    protected $table = 'qontak_settings';

    protected $fillable = [
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
        'sales_template_h1_vars',
        'sales_template_h7_vars',
        'sales_template_1month_vars',
        'pending_template_h1_vars',
        'pending_template_h7_vars',
        'pending_template_1month_vars',
        'variable_mappings',
    ];

    protected $casts = [
        'variable_mappings' => 'array',
    ];

    /**
     * Retrieve the singleton settings record, creating it with defaults if missing.
     */
    public static function getSettings(): self
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::create([
                'base_url' => 'https://service-chat.qontak.com',
            ]);
        }
        return $settings;
    }
}
