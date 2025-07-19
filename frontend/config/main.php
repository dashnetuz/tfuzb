<?php
require_once __DIR__ . '/../helpers/helpers.php'; // Avval chaqirib olamiz

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'language' => 'uz',
    'bootstrap' => ['log', \frontend\components\LanguageSelector::class],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'baseUrl' => '',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-frontend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'class' => \frontend\components\UrlManager::class,
            'languages' => ['uz', 'ru', 'en'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableDefaultLanguageUrlCode' => true,
            'rules' => [
                'dashboard/courses' => 'dashboard/courses',
                'dashboard/course/view/<id:\d+>' => 'dashboard/course-view',
                'dashboard/lesson/view/<id:\d+>' => 'dashboard/lesson-view',
                'dashboard/part/view/<id:\d+>' => 'dashboard/part-view',
                'dashboard/mark-part-complete' => 'dashboard/mark-part-complete',

                'dashboard/<type:(category|course|lesson|part)>/<action:(index|create|update|delete)>' => 'dashboard/<type><action>',
                # role:
                'dashboard/admin-user/manage-roles/<id:\d+>' => 'dashboard/admin-user-manage-roles',
                'dashboard/manage-role-permissions/<role:\w+>' => 'dashboard/manage-role-permissions',

                '' => 'site/index',
                'dashboard' => 'dashboard/index',
                'dashboard-admin' => 'dashboard-admin/index',
                'login' => 'auth/login',
                'register' => 'auth/register',
                'request-password-reset' => 'auth/request-password-reset',
                'reset-password/<token:[\w\-]+>' => 'auth/reset-password',

                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller>/<action>' => '<controller>/<action>',
                '<controller>' => '<controller>/index',
            ],
        ],
    ],
    'modules' => [],
    'aliases' => [],
    'params' => $params,
    'container' => [],
];
