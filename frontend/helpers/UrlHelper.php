<?php

namespace frontend\helpers;

use Yii;
use yii\helpers\Url;

class UrlHelper
{
    /**
     * Berilgan route va parametrlar bilan tilni qo‘shib URL yaratadi
     */
    public static function createLanguageUrl($lang)
    {
        $request = Yii::$app->request;
        $pathInfo = $request->pathInfo; // uz/dashboard/admin-setting yoki en/...

        // pathInfo ni explode qilamiz
        $segments = explode('/', $pathInfo);

        // Birinchi segment til bo‘lsa, olib tashlaymiz
        if (in_array($segments[0], ['uz', 'ru', 'en'])) {
            array_shift($segments);
        }

        // URL ni qayta quramiz
        $newPath = implode('/', $segments);
        return '/' . $lang . '/' . $newPath;
    }




    /**
     * Route va parametrlar bilan URL yaratadi, lekin tilni ham kiritadi
     */
    public static function toRouteWithLang($route, $params = [])
    {
        return Url::toRoute(array_merge([$route], $params));
    }
}