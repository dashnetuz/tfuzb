<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quiz_question".
 *
 * @property int $id
 * @property int $quiz_id
 * @property string $body
 * @property string|null $explanation
 * @property int $order
 * @property string $created_at
 *
 * @property Quiz $quiz
 * @property QuizOption[] $options
 */
class QuizQuestion extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%quiz_question}}';
    }

    public function rules()
    {
        return [
            [['quiz_id', 'body'], 'required'],
            [['quiz_id', 'order'], 'integer'],
            [['body', 'explanation'], 'string'],
            [['created_at'], 'safe'],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quiz::class, 'targetAttribute' => ['quiz_id' => 'id']],
        ];
    }

    public function getQuiz()
    {
        return $this->hasOne(Quiz::class, ['id' => 'quiz_id']);
    }

    public function getOptions()
    {
        return $this->hasMany(QuizOption::class, ['question_id' => 'id']);
    }
}
