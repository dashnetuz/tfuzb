<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property UserRole[] $userRoles
 */
class Role extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%role}}';
    }

    public function rules()
    {
        return [
            [['name', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    public function getUserRoles()
    {
        return $this->hasMany(UserRole::class, ['role_id' => 'id']);
    }

    public static function getAllNames()
    {
        return self::find()->select('name')->indexBy('id')->column();
    }

    public static function getIdByName($name)
    {
        return self::find()->select('id')->where(['name' => $name])->scalar();
    }

    public function getPermissions()
    {
        return $this->hasMany(Permission::class, ['id' => 'permission_id'])
            ->viaTable('role_permission', ['role_id' => 'id']);
    }

}
