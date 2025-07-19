<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quiz_attempt".
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $user_id
 * @property string $started_at
 * @property string|null $ended_at
 * @property float|null $score
 * @property bool|null $is_passed
 * @property int $try_index
 *
 * @property Quiz $quiz
 * @property User $user
 * @property QuizAnswer[] $answers
 */
class QuizAttempt extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%quiz_attempt}}';
    }

    public function rules()
    {
        return [
            [['quiz_id', 'user_id', 'started_at', 'try_index'], 'required'],
            [['quiz_id', 'user_id', 'try_index'], 'integer'],
            [['score'], 'number'],
            [['started_at', 'ended_at'], 'safe'],
            [['is_passed'], 'boolean'],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quiz::class, 'targetAttribute' => ['quiz_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id' => 'user_id']],
        ];
    }

    public function getQuiz()
    {
        return $this->hasOne(Quiz::class, ['id' => 'quiz_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(QuizAnswer::class, ['attempt_id' => 'id']);
    }
}
