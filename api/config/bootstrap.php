<?php
$dotenvPath = dirname(__DIR__, 2); // /var/www/html
if (file_exists($dotenvPath . '/.env')) {
    Dotenv\Dotenv::createImmutable($dotenvPath)->safeLoad();
}
