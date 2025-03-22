<?php

namespace frontend\controllers;

use yii\web\Controller;

class AuthController extends Controller
{
    public function actionCallback()
    {
        return $this->render('callback');
    }
}
