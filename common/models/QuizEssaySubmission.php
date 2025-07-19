<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "quiz_essay_submission".
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $part_id
 * @property int $user_id
 * @property string|null $essay_text
 * @property bool $is_submitted
 * @property bool $is_checked
 * @property int|null $score
 * @property int $total
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property Quiz $quiz
 * @property Part $part
 */
class QuizEssaySubmission extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%quiz_essay_submission}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['quiz_id', 'part_id', 'user_id'], 'required'],
            [['quiz_id', 'part_id', 'user_id', 'score', 'total', 'created_at', 'updated_at'], 'integer'],
            [['essay_text'], 'string'],
            [['is_submitted', 'is_checked'], 'boolean'],
            [['total'], 'default', 'value' => 100],
            [['is_submitted', 'is_checked'], 'default', 'value' => false],
            [['submitted_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'quiz_id' => Yii::t('app', 'Essay Quiz ID'),
            'part_id' => Yii::t('app', 'Part ID'),
            'user_id' => Yii::t('app', 'Foydalanuvchi'),
            'essay_text' => Yii::t('app', 'Yozgan matn'),
            'is_submitted' => Yii::t('app', 'Yuborildimi'),
            'is_checked' => Yii::t('app', 'Baholanganmi'),
            'score' => Yii::t('app', 'Olingan ball'),
            'total' => Yii::t('app', 'Maksimal ball'),
            'created_at' => Yii::t('app', 'Yaratilgan vaqt'),
            'updated_at' => Yii::t('app', 'Yangilangan vaqt'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getQuiz()
    {
        return $this->hasOne(Quiz::class, ['id' => 'quiz_id']);
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }
}
