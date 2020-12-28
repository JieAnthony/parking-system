<?php

return [
    'page' => [
        'limit' => env('INFO_PAGE_LIMIT', 20),
    ],
    'sms_length' => env('INFO_SMS_CODE_LENGTH', 6),
    'sms_code_template' => env('INFO_SMS_CODE_TEMPLATE'),
];
