<?php

namespace common\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username'], 'string', 'min' => 4, 'max' => 255],
            [['password'], 'string', 'min' => 6],
            [['username'], 'unique', 'targetClass' => '\common\models\User', 'message' => 'Bu username allaqachon band.'],
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->auth_type = 'local';
        $user->status = User::STATUS_ACTIVE;

        return $user->save() ? $user : null;
    }
}
