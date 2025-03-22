<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Profile model
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $tell
 * @property string|null $birth_date
 *
 * @property User $user
 */
class Profile extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%profile}}';
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'tell'], 'string', 'max' => 255],
            [['birth_date'], 'date', 'format' => 'php:Y-m-d'],
            [['user_id'], 'unique'], // Har bir user faqat bitta profilega ega bo'lishi mumkin
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
