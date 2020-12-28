<?php

return [
    'ali' => [
        'app_id' => env('ALI_PAY_APP_ID'),
        'ali_public_key' => env('ALI_PAY_ALI_PUBLIC_KEY'),
        'private_key' => env('ALI_PAY_PRIVATE_KEY'),
        'log' => [
            'file' => storage_path('logs/ali_pay.log'),
        ],
    ],
    'wechat' => [
        'app_id' => env('WECHAT_PAY_APP_ID'),
        'mch_id' => env('WECHAT_PAY_MCH_ID'),
        'miniapp_id' => env('WECHAT_PAY_MINIAPP_ID'),
        'key' => env('WECHAT_PAY_KEY'),
        'cert_client' => env('WECHAT_PAY_CERT_CLIENT'),
        'cert_key' => env('WECHAT_PAY_CERT_KEY'),
        'log' => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
