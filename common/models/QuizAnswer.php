<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quiz_answer".
 *
 * @property int $id
 * @property int $attempt_id
 * @property int $question_id
 * @property int|null $option_id
 * @property string|null $answer_text
 * @property bool|null $is_correct
 * @property string|null $feedback
 *
 * @property QuizAttempt $attempt
 * @property QuizQuestion $question
 * @property QuizOption|null $option
 */
class QuizAnswer extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%quiz_answer}}';
    }

    public function rules()
    {
        return [
            [['attempt_id', 'question_id'], 'required'],
            [['attempt_id', 'question_id', 'option_id'], 'integer'],
            [['answer_text', 'feedback'], 'string'],
            [['is_correct'], 'boolean'],
            [['attempt_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuizAttempt::class, 'targetAttribute' => ['attempt_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuizQuestion::class, 'targetAttribute' => ['id' => 'question_id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuizOption::class, 'targetAttribute' => ['id' => 'option_id']],
        ];
    }

    public function getAttempt()
    {
        return $this->hasOne(QuizAttempt::class, ['id' => 'attempt_id']);
    }

    public function getQuestion()
    {
        return $this->hasOne(QuizQuestion::class, ['id' => 'question_id']);
    }

    public function getOption()
    {
        return $this->hasOne(QuizOption::class, ['id' => 'option_id']);
    }
}
