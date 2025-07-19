<?php

use frontend\helpers\UrlHelper;

function urlLang($route, $params = []) {
    return UrlHelper::toRouteWithLang($route, $params);
}
