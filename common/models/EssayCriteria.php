<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "essay_criteria".
 *
 * @property int $id
 * @property int $quiz_id
 * @property string $title
 * @property string|null $description
 * @property int $weight
 *
 * @property Quiz $quiz
 */
class EssayCriteria extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%essay_criteria}}';
    }

    public function rules()
    {
        return [
            [['quiz_id', 'title', 'weight'], 'required'],
            [['quiz_id', 'weight'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['quiz_id'], 'exist', 'targetClass' => Quiz::class, 'targetAttribute' => ['quiz_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quiz_id' => 'Quiz',
            'title' => 'Mezon nomi',
            'description' => 'Izoh (ixtiyoriy)',
            'weight' => 'Baholash foizi (%)',
        ];
    }

    public function getQuiz()
    {
        return $this->hasOne(Quiz::class, ['id' => 'quiz_id']);
    }
}
