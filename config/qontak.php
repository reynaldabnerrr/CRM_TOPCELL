<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mekari Qontak API Configurations
    |--------------------------------------------------------------------------
    */
    'base_url' => env('QONTAK_BASE_URL', 'https://api.mekari.com'),
    
    'client_id' => env('QONTAK_CLIENT_ID'),
    
    'client_secret' => env('QONTAK_CLIENT_SECRET'),
    
    'channel_integration_id' => env('QONTAK_CHANNEL_INTEGRATION_ID'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Message Templates (UUID)
    |--------------------------------------------------------------------------
    | UUID of approved WhatsApp templates registered in Mekari Qontak.
    */
    'templates' => [
        // Sales Aftercare Templates
        'sales' => [
            'h1'     => env('QONTAK_TEMPLATE_SALES_H1'),
            'h7'     => env('QONTAK_TEMPLATE_SALES_H7'),
            '1month' => env('QONTAK_TEMPLATE_SALES_1MONTH'),
        ],
        
        // Pending Customers Templates
        'pending' => [
            'h1'      => env('QONTAK_TEMPLATE_PENDING_H1'),
            'h7'      => env('QONTAK_TEMPLATE_PENDING_H7'),
            '1month'  => env('QONTAK_TEMPLATE_PENDING_1MONTH'),
        ],
    ],
];
