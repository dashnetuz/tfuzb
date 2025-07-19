<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Course extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%course}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['category_id', 'title_uz', 'title_ru', 'title_en', 'url_uz', 'url_ru', 'url_en'], 'required', 'message' => Yii::t('app', 'Maydon to‘ldirilishi shart')],
            [['description_uz', 'description_ru', 'description_en'], 'string'],
            [['position', 'category_id', 'user_id'], 'integer'],
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
            'category_id' => Yii::t('app', 'Kategoriya'),
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

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

//    public function getLessons()
//    {
//        return $this->hasMany(Lesson::class, ['course_id' => 'id']);
//    }
    public function getLessons()
    {
        return $this->hasMany(Lesson::class, ['course_id' => 'id'])
            ->andWhere(['is_active' => true])
            ->orderBy(['position' => SORT_ASC]); // tartib bo‘yicha ixtiyoriy
    }

    public function getTitle()
    {
        return $this->{'title_' . Yii::$app->language} ?? $this->title_uz;
    }

    public function getDescription()
    {
        return $this->{'description_' . Yii::$app->language} ?? $this->description_uz;
    }

    public function getLessonCount()
    {
        return $this->getLessons()->count();
    }

    public function getPartCount()
    {
        return \common\models\Part::find()->where(['lesson_id' => $this->getLessons()->select('id')])->count();
    }
    public function getVideoCount()
    {
        return \common\models\PartContent::find()
            ->where(['type_id' => 3]) // video
            ->andWhere([
                'part_id' => \common\models\Part::find()
                    ->select('id')
                    ->where([
                        'lesson_id' => \common\models\Lesson::find()
                            ->select('id')
                            ->where(['course_id' => $this->id])
                    ])
            ])
            ->count();
    }


}
