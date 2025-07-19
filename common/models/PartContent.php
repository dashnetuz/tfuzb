<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * @property int $id
 * @property int $part_id
 * @property int $type_id
 * @property int $position
 * @property int|null $user_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Part $part
 * @property ContentType $type
 * @property User $user
 * @property ContentText $textContent
 * @property ContentPicture $pictureContent
 * @property ContentVideo $videoContent
 * @property ContentPdf $pdfContent
 */
class PartContent extends ActiveRecord
{
    /**
     * @var mixed|null
     */
    public $activeContent;

    public static function tableName()
    {
        return '{{%part_content}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['part_id', 'type_id'], 'required'],
            [['part_id', 'type_id', 'position', 'user_id', 'status'], 'integer'],
        ];
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }

    public function getType()
    {
        return $this->hasOne(ContentType::class, ['id' => 'type_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }



    public function getTextContent()
    {
        return $this->hasOne(ContentText::class, ['part_content_id' => 'id']);
    }

    public function getPictureContent()
    {
        return $this->hasOne(ContentPicture::class, ['part_content_id' => 'id']);
    }

    public function getVideoContent()
    {
        return $this->hasOne(ContentVideo::class, ['part_content_id' => 'id']);
    }

    public function getPdfContent()
    {
        return $this->hasOne(ContentPdf::class, ['part_content_id' => 'id']);
    }

    /**
     * @return ContentText|ContentPicture|ContentVideo|ContentPdf|null
     */
    public function getActiveContent()
    {
        if (YII_ENV_DEV) {
            Yii::info('--- ACTIVE CONTENT DIAGNOSTIKA ---', __METHOD__);
            Yii::info('type_id: ' . $this->type_id, __METHOD__);
        }

        $typeModel = $this->type ?? ContentType::findOne($this->type_id);
        if (!$typeModel) {
            Yii::error('type model topilmadi', __METHOD__);
            return null;
        }

        $typeName = $typeModel->name;

        if (YII_ENV_DEV) {
            Yii::info('Aniqlangan typeName: ' . $typeName, __METHOD__);
        }

        return match ($typeName) {
            'text' => $this->textContent,
            'picture' => $this->pictureContent,
            'video' => $this->videoContent,
            'pdf' => $this->pdfContent,
            default => null,
        };
    }






}
