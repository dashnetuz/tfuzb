<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_part_progress".
 *
 * @property int $id
 * @property int $user_id
 * @property int $part_id
 * @property bool|null $is_completed
 * @property string|null $completed_at
 *
 * @property User $user
 * @property Part $part
 */
class UserPartProgress extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_part_progress}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'part_id'], 'required'],
            [['user_id', 'part_id'], 'integer'],
            [['is_completed'], 'boolean'],
            [['completed_at'], 'safe'],
            [['user_id', 'part_id'], 'unique', 'targetAttribute' => ['user_id', 'part_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::class, 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'Foydalanuvchi'),
            'part_id' => Yii::t('app', 'Part'),
            'is_completed' => Yii::t('app', 'Tugallangan'),
            'completed_at' => Yii::t('app', 'Tugallangan vaqti'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }
}
