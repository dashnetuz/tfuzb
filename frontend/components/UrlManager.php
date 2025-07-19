<?php

namespace frontend\components;

use codemix\localeurls\UrlManager as BaseLocaleUrlManager;

class UrlManager extends BaseLocaleUrlManager
{
    public function createUrl($params)
    {
        return parent::createUrl($params);
    }
}
