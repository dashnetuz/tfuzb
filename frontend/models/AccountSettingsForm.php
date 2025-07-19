<?php

namespace frontend\models;

use yii\base\Model;

class AccountSettingsForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $firstname;
    public $lastname;
    public $avatar;
    public $roles = []; // array

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username', 'email', 'firstname', 'lastname', 'avatar'], 'string', 'max' => 255],
            [['firstname', 'lastname'], 'required'],
            [['roles'], 'safe'],
        ];
    }
}
