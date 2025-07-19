<?php
return [
    // boshqa sozlamalar...
    'jwtSecret' => getenv('APP_SECRET'),
    'jwtIssuer' => getenv('APP_ISSUER'),
    'jwtAudience' => getenv('APP_AUDIENCE'),
    'jwtExpire' => (int) getenv('JWT_EXPIRE'),
];
