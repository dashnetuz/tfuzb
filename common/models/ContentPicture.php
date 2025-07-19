<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $part_content_id
 * @property string $file_path
 * @property string|null $alt
 * @property string|null $caption
 */
class ContentPicture extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%content_picture}}';
    }

    public function rules()
    {
        return [
            [['part_content_id', 'file_path'], 'required'],
            [['file_path', 'alt', 'caption'], 'string'],
            [['part_content_id'], 'unique'],
        ];
    }

    public function getPartContent()
    {
        return $this->hasOne(PartContent::class, ['id' => 'part_content_id']);
    }
    public function getImageUrl()
    {
        return \Yii::getAlias($this->file_path);
    }

}
