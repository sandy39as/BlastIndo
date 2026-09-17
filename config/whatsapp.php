<?php

return [
    'api_url' => env('META_WA_URL', 'https://graph.facebook.com/v20.0'),
    'phone_number_id' => env('META_WA_PHONE_NUMBER_ID'),
    'business_account_id' => env('META_WA_BUSINESS_ACCOUNT_ID'),
    'access_token' => env('META_WA_ACCESS_TOKEN'),
    'webhook_verify_token' => env('META_WA_WEBHOOK_VERIFY_TOKEN', 'blastindo_secret_token_123'),
];
