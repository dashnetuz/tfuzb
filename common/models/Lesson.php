<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Lesson extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%lesson}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['course_id', 'title_uz', 'title_ru', 'title_en', 'url_uz', 'url_ru', 'url_en'], 'required', 'message' => Yii::t('app', 'Maydon to‘ldirilishi shart')],
            [['description_uz', 'description_ru', 'description_en'], 'string'],
            [['position', 'course_id', 'user_id'], 'integer'],
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
            'course_id' => Yii::t('app', 'Kurs'),
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

    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }

    public function getPartCount()
    {
        return $this->hasMany(Part::class, ['lesson_id' => 'id'])->count();
    }

    public function getParts()
    {
        return $this->hasMany(Part::class, ['lesson_id' => 'id']);
    }

    public function getTitle()
    {
        return $this->{'title_' . Yii::$app->language} ?? $this->title_uz;
    }

    public function getDescription()
    {
        return $this->{'description_' . Yii::$app->language} ?? $this->description_uz;
    }

    public function isInProgressByUser($userId)
    {
        foreach ($this->parts as $part) {
            // Tekshir: test mavjudmi va o'tilmaganmi
            if ($part->test) {
                $lastAttempt = (new \yii\db\Query())
                    ->from('quiz_attempt')
                    ->where(['user_id' => $userId, 'quiz_id' => $part->test->id])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                if (!$lastAttempt || !$lastAttempt['is_passed']) {
                    return true; // test o'tilmagan
                }
            }

            // Tekshir: esse mavjudmi va topshirilmaganmi
            if ($part->essay) {
                $submission = (new \yii\db\Query())
                    ->from('quiz_essay_submission')
                    ->where(['user_id' => $userId, 'part_id' => $part->id])
                    ->one();

                if (!$submission || !$submission['is_submitted'] || is_null($submission['score'])) {
                    return true; // esse hali topshirilmagan yoki baholanmagan
                }
            }
        }

        // Agar barcha testlar va esse'lar topshirilgan bo‘lsa — bu dars tamom
        return false;
    }

}
