<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-restapi',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'restapi\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
//            'baseUrl' => $_ENV['RESTAPI_URL'],
            'baseUrl' => '/',
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'user'
                    ]
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false, // REST API da odatda yoqilmaydi
            'enableSession' => false, // API uchun sessiyalar ishlatilmaydi
            'loginUrl' => null, // REST API da redirect yo‘q
        ],
    ],
    'params' => $params,
];
