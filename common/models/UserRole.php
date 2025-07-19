<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class UserRole extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_role}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'role_id'], 'required'],
            [['user_id', 'role_id'], 'integer'],
            [['role_id'], 'exist', 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    public static function getRoleIdByName($name)
    {
        return (new Query())
            ->select('id')
            ->from('{{%role}}')
            ->where(['name' => $name])
            ->scalar();
    }
}
