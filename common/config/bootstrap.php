<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api'); // ✅ BU MUHIM
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

$dotenvPath = dirname(__DIR__, 2);
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();
}
