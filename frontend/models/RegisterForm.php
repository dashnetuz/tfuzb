<?php

namespace frontend\models;

use yii\base\Model;
use Yii;

class RegisterForm extends Model
{
    public $username;
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required', 'message' => Yii::t('app', '{attribute} to‘ldirilishi shart.')],
            ['email', 'email', 'message' => Yii::t('app', 'Email formati noto‘g‘ri.')],
            [['username', 'email'], 'string', 'max' => 255],
            ['password', 'string', 'min' => 8],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email Address'),
            'password' => Yii::t('app', 'Password'),
        ];
    }
}
