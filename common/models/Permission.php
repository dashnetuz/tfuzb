<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Permission extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%permission}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }
}
