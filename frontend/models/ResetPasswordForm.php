<?php

namespace frontend\models;

use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $current_password;
    public $new_password;
    public $confirm_password;

    public function rules()
    {
        return [
            [['current_password', 'new_password', 'confirm_password'], 'required'],
            [['current_password', 'new_password', 'confirm_password'], 'string', 'min' => 8, 'max' => 255],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password', 'message' => 'Yangi parol va tasdiqlash paroli mos emas.'],
        ];
    }
}
