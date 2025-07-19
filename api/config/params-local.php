<?php
return [
    'baseUrl' => getenv('API_URL') ?: 'http://localhost:8183',

    'user.passwordResetTokenExpire' => 3600,

    'jwtIssuer' => getenv('APP_ISSUER') ?: 'tfuzb-api',
    'jwtAudience' => getenv('APP_AUDIENCE') ?: 'frontend-app',
    'jwtSecret' => getenv('APP_SECRET') ?: 'your_secret_key',
    'jwtExpire' => getenv('JWT_EXPIRE') ?: 86400,

    'googleClientId' => getenv('GOOGLE_CLIENT_ID'),
    'googleClientSecret' => getenv('GOOGLE_CLIENT_SECRET'),
    'facebookClientId' => getenv('FACEBOOK_CLIENT_ID'),
    'facebookClientSecret' => getenv('FACEBOOK_CLIENT_SECRET'),
];
