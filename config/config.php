<?php
// config/config.php

return [
    'api' => [
        'base_url' => getenv('API_BASE_URL'), // A URL onde API FastAPI está rodando
        'token'    => getenv('API_TOKEN')   // O mesmo token definido no .env da API
    ],
    'recaptcha' => [
        'site_key'   => getenv('RECAPCTHA_SITE_KEY'),
        'secret_key' => getenv('RECAPCTHA_PRIVATE_KEY')
    ]
];