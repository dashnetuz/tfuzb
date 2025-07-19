<?php

namespace frontend\helpers;

class AuthHelper
{
    public static function getToken()
    {
        return \Yii::$app->session->get('jwt_token', null);
    }

    public static function getUser(): ?array
    {
        return \Yii::$app->view->params['user'] ?? null;
    }

    public static function hasRole(string $role): bool
    {
        $user = self::getUser();
        return $user && in_array($role, $user['roles'] ?? []);
    }
}
