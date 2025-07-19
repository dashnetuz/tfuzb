<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Category extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['title_uz', 'title_ru', 'title_en', 'url_uz', 'url_ru', 'url_en'], 'required', 'message' => Yii::t('app', 'Maydon to‘ldirilishi shart')],
            [['description_uz', 'description_ru', 'description_en'], 'string'],
            [['position', 'user_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['picture'], 'file', 'extensions' => 'jpg, png, jpeg, webp', 'maxSize' => 2*1024*1024, 'skipOnEmpty' => true],
            [['url_uz', 'url_ru', 'url_en'], 'string', 'max' => 255],
            [['title_uz', 'title_ru', 'title_en'], 'string', 'max' => 255],
            [['url_uz', 'url_ru', 'url_en'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title_uz' => Yii::t('app', 'Sarlavha (UZ)'),
            'title_ru' => Yii::t('app', 'Sarlavha (RU)'),
            'title_en' => Yii::t('app', 'Sarlavha (EN)'),
            'description_uz' => Yii::t('app', 'Tavsif (UZ)'),
            'description_ru' => Yii::t('app', 'Tavsif (RU)'),
            'description_en' => Yii::t('app', 'Tavsif (EN)'),
            'url_uz' => Yii::t('app', 'URL (UZ)'),
            'url_ru' => Yii::t('app', 'URL (RU)'),
            'url_en' => Yii::t('app', 'URL (EN)'),
            'picture' => Yii::t('app', 'Rasm'),
            'position' => Yii::t('app', 'Tartib'),
            'is_active' => Yii::t('app', 'Faollik'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $userId = Yii::$app->session->get('user_profile')['id'] ?? null;

            // Yangi yozuvda user_id biriktiramiz
            if ($this->isNewRecord && $userId) {
                $this->user_id = $userId;
            }

            // Yangi yozuvda position avtomatik tayinlanadi
            if ($this->isNewRecord && empty($this->position)) {
                $maxPosition = self::find()->max('position');
                $this->position = $maxPosition ? $maxPosition + 1 : 1;
            }

            return true;
        }
        return false;
    }

    public function getCourses()
    {
        return $this->hasMany(Course::class, ['category_id' => 'id']);
    }

    public function getTitle()
    {
        return $this->{'title_' . Yii::$app->language} ?? $this->title_uz;
    }

    public function getDescription()
    {
        return $this->{'description_' . Yii::$app->language} ?? $this->description_uz;
    }
}
