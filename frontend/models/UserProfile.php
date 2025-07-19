<?php

namespace frontend\models;

use yii\base\Model;
use Yii;

class UserProfile extends Model
{
    public $id;
    public $username;
    public $email;
    public $firstname;
    public $lastname;
    public $avatar;
    public $roles = [];

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username', 'email', 'firstname', 'lastname', 'avatar'], 'string'],
            [['roles'], 'safe'],
        ];
    }
}
