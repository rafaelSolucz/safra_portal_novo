<?php
// config/config.php

return [
    'api' => [
        'base_url' => getenv('API_BASE_URL'), // A URL onde a API FastAPI (ou outra) está rodando
        'token'    => getenv('API_TOKEN')   // O token definido no .env da API
    ],
    'recaptcha' => [
        'site_key'   => getenv('RECAPTCHA_SITE_KEY'),
        'secret_key' => getenv('RECAPTCHA_PRIVATE_KEY')
    ]
];