<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quiz_option".
 *
 * @property int $id
 * @property int $question_id
 * @property string $body
 * @property bool $is_correct
 *
 * @property QuizQuestion $question
 */
class QuizOption extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%quiz_option}}';
    }

    public function rules()
    {
        return [
            [['question_id', 'body'], 'required'],
            [['question_id'], 'integer'],
            [['is_correct'], 'boolean'],
            [['body'], 'string', 'max' => 255],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuizQuestion::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    public function getQuestion()
    {
        return $this->hasOne(QuizQuestion::class, ['id' => 'question_id']);
    }
}
