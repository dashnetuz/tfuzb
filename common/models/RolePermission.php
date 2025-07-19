<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * RolePermission modeli — rollarga permission biriktirish uchun
 */
class RolePermission extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%role_permission}}';
    }

    public function rules()
    {
        return [
            [['role_id', 'permission_id'], 'required'],
            [['role_id', 'permission_id'], 'integer'],
            [['role_id', 'permission_id'], 'unique', 'targetAttribute' => ['role_id', 'permission_id']],
            [['role_id'], 'exist', 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
            [['permission_id'], 'exist', 'targetClass' => Permission::class, 'targetAttribute' => ['permission_id' => 'id']],
        ];
    }

    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    public function getPermission()
    {
        return $this->hasOne(Permission::class, ['id' => 'permission_id']);
    }
}
