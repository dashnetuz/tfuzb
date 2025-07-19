<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Part extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%part}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['lesson_id', 'title_uz', 'title_ru', 'title_en', 'url_uz', 'url_ru', 'url_en'], 'required', 'message' => Yii::t('app', 'Maydon to‘ldirilishi shart')],
            [['description_uz', 'description_ru', 'description_en'], 'string'],
            [['position', 'lesson_id', 'user_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['url_uz', 'url_ru', 'url_en'], 'string', 'max' => 255],
            [['title_uz', 'title_ru', 'title_en'], 'string', 'max' => 255],
            [['url_uz', 'url_ru', 'url_en'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'lesson_id' => Yii::t('app', 'Dars'),
            'title_uz' => Yii::t('app', 'Sarlavha (UZ)'),
            'title_ru' => Yii::t('app', 'Sarlavha (RU)'),
            'title_en' => Yii::t('app', 'Sarlavha (EN)'),
            'description_uz' => Yii::t('app', 'Tavsif (UZ)'),
            'description_ru' => Yii::t('app', 'Tavsif (RU)'),
            'description_en' => Yii::t('app', 'Tavsif (EN)'),
            'url_uz' => Yii::t('app', 'URL (UZ)'),
            'url_ru' => Yii::t('app', 'URL (RU)'),
            'url_en' => Yii::t('app', 'URL (EN)'),
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

    public function getLesson()
    {
        return $this->hasOne(Lesson::class, ['id' => 'lesson_id']);
    }

    public function getTitle()
    {
        return $this->{'title_' . Yii::$app->language} ?? $this->title_uz;
    }

    public function getDescription()
    {
        return $this->{'description_' . Yii::$app->language} ?? $this->description_uz;
    }

    public function getSortedContents()
    {
        $contents = PartContent::find()
            ->where(['part_id' => $this->id, 'status' => 1])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        foreach ($contents as $content) {
            $content->activeContent = $content->getActiveContent();
        }

        return $contents;
    }

    public function getQuizzes()
    {
        return $this->hasMany(Quiz::class, ['part_id' => 'id']);
    }

    public function getTest()
    {
        return $this->hasOne(Quiz::class, ['part_id' => 'id'])->andWhere(['type' => 1]);
    }

    public function getEssay()
    {
        return $this->hasOne(Quiz::class, ['part_id' => 'id'])->andWhere(['type' => 2]);
    }



}
