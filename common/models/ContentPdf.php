<?php

namespace common\models;

use yii\db\ActiveRecord;

class ContentPdf extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%content_pdf}}';
    }

    public function rules()
    {
        return [
            [['part_content_id', 'file_path'], 'required'],
            [['title_uz', 'title_ru', 'title_en'], 'string', 'max' => 255],
            [['description_uz', 'description_ru', 'description_en'], 'string'],
            [['part_content_id'], 'unique'],
        ];
    }

    public function getPartContent()
    {
        return $this->hasOne(PartContent::class, ['id' => 'part_content_id']);
    }

    public function getTitle()
    {
        return $this->{'title_' . \Yii::$app->language} ?? $this->title_uz;
    }

    public function getDescription()
    {
        return $this->{'description_' . \Yii::$app->language} ?? $this->description_uz;
    }
    public function getPdfUrl()
    {
        return \Yii::getAlias($this->file_path);
    }

}
