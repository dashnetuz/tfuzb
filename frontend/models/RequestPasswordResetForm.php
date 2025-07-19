<?php

namespace frontend\models;

use yii\base\Model;
use Yii;

class RequestPasswordResetForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            [['email'], 'required', 'message' => Yii::t('app', '{attribute} to‘ldirilishi shart.')],
            ['email', 'email', 'message' => Yii::t('app', 'Email formati noto‘g‘ri.')],
            ['email', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email Address'),
        ];
    }
}
