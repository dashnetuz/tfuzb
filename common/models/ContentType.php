<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class ContentType extends ActiveRecord
{
    const TYPE_TEXT = 1;
    const TYPE_PICTURE = 2;
    const TYPE_VIDEO = 3;
    const TYPE_PDF = 4;
    public static function tableName()
    {
        return '{{%content_type}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['label'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['label'], 'string', 'max' => 100],
            [['name'], 'unique'],
        ];
    }


}
