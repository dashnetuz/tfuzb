<?php

namespace frontend\models;

use yii\base\Model;
use Yii;

class LoginForm extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'message' => Yii::t('app', '{attribute} to‘ldirilishi shart.')],
            [['username'], 'string', 'min' => 3, 'max' => 255],
            [['password'], 'string', 'min' => 8, 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
        ];
    }
}
