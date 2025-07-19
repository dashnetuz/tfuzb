<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $part_content_id
 * @property string|null $text_uz
 * @property string|null $text_ru
 * @property string|null $text_en
 */
class ContentText extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%content_text}}';
    }

    public function rules()
    {
        return [
            [['part_content_id'], 'required'],
            [['text_uz', 'text_ru', 'text_en'], 'string'],
            [['part_content_id'], 'unique'],
        ];
    }

    public function getPartContent()
    {
        return $this->hasOne(PartContent::class, ['id' => 'part_content_id']);
    }

    public function getText()
    {
        return $this->{'text_' . \Yii::$app->language} ?? $this->text_uz;
    }
}
