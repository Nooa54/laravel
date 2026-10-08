<?php

return [
    // Admin-valikon salasana (aseta .env-tiedostoon ADMIN_PASSWORD)
    'admin_password' => env('ADMIN_PASSWORD', 'lepaa2026'),

    // Chatbot (Claude). Hae avain osoitteesta https://console.anthropic.com
    'anthropic_key'   => env('ANTHROPIC_API_KEY'),
    'anthropic_model' => env('ANTHROPIC_MODEL', 'claude-sonnet-5-5'),

    'name'    => 'Matkakoti Lepää rauhassa',
    'phone'   => '+358 40 123 4567',
    'email'   => 'info@lepaarauhassa.example',
    'address' => 'Maatilantie 12, 29600 Noormarkku',
];
