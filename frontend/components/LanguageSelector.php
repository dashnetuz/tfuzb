<?php

namespace frontend\components;

use Yii;
use yii\base\BootstrapInterface;

class LanguageSelector implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $lang = $app->urlManager->parseRequest($app->getRequest())[1]['lang'] ?? null;

        if (in_array($lang, ['uz', 'ru', 'en'])) {
            $app->language = $lang;
        }
    }
}

