<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quiz".
 *
 * @property int $id
 * @property int $part_id
 * @property string $title
 * @property string|null $description
 * @property int $type
 * @property int $time_limit
 * @property int $pass_percent
 * @property int|null $max_attempt
 * @property string $created_at
 *
 * @property Part $part
 * @property QuizQuestion[] $questions
 * @property QuizAttempt[] $attempts
 */
class Quiz extends ActiveRecord
{
    const TYPE_TEST = 1;
    const TYPE_ESSAY = 2;

    public static function tableName()
    {
        return '{{%quiz}}';
    }

    public function rules()
    {
        return [
            [['part_id', 'title', 'type'], 'required'],
            [['part_id', 'type', 'time_limit', 'pass_percent', 'max_attempt'], 'integer'],
            [['description'], 'string'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::class, 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }

    public function getQuestions()
    {
        return $this->hasMany(QuizQuestion::class, ['quiz_id' => 'id']);
    }

    public function getAttempts()
    {
        return $this->hasMany(QuizAttempt::class, ['quiz_id' => 'id']);
    }

    public function getAttemptStats($userId)
    {
        $data = [
            'quiz_id' => $this->id,
            'quiz_title' => $this->title,
            'type' => $this->type,
            'total_questions' => 0,
            'total_attempts' => 0,
            'pass_percent' => $this->pass_percent,
            'max_attempt' => $this->max_attempt,
            'attempts' => [],
            'last_score' => null,
            'last_percent' => null,
            'last_passed' => null,
            'status' => 'not_attempted',
            'part_id' => null,
            'part_title' => null,
            'lesson_id' => null,
            'lesson_title' => null,
        ];

        // 🔗 Bog‘langan part va lesson
        $part = $this->part ?? null;
        if ($part) {
            $data['part_id'] = $part->id;
            $data['part_title'] = $part->title;
            $data['lesson_id'] = $part->lesson->id ?? null;
            $data['lesson_title'] = $part->lesson->title ?? null;
        }

        // 🎯 Agar test bo‘lsa
        if ((int)$this->type === Quiz::TYPE_TEST) {
            $questions = $this->getQuestions()->all();
            $data['total_questions'] = count($questions);

            $attempts = QuizAttempt::find()
                ->where(['quiz_id' => $this->id, 'user_id' => $userId])
                ->orderBy(['id' => SORT_ASC])
                ->all();

            foreach ($attempts as $attempt) {
                $percent = $attempt->score;

                $data['attempts'][] = [
                    'id' => $attempt->id,
                    'score' => $attempt->score,
                    'percent' => $percent,
                    'is_passed' => $attempt->is_passed,
                    'started_at' => $attempt->started_at,
                    'ended_at' => $attempt->ended_at,
                    'try_index' => $attempt->try_index,
                ];

                // So‘nggi natija (baholangan)
                if ($attempt->ended_at !== null) {
                    $data['last_score'] = $attempt->score;
                    $data['last_percent'] = $percent;
                    $data['last_passed'] = $attempt->is_passed;
                }
            }

            $data['total_attempts'] = count($data['attempts']);
            if ($data['total_attempts'] === 0) {
                $data['status'] = 'not_attempted';
            } elseif ($data['last_score'] === null && $data['last_percent'] === null) {
                $data['status'] = 'pending_review';
            } elseif ($data['last_passed']) {
                $data['status'] = 'passed';
            } else {
                $data['status'] = 'failed';
            }

        }

        // ✍️ Agar esse bo‘lsa
        elseif ((int)$this->type === Quiz::TYPE_ESSAY) {
            $submission = QuizEssaySubmission::find()
                ->where([
                    'quiz_id' => $this->id,
                    'user_id' => $userId,
                    'part_id' => $this->part_id,
                    'is_submitted' => true
                ])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            if ($submission) {
                $data['total_questions'] = $submission->total;
                $data['last_score'] = $submission->score;
                $data['last_percent'] = ($submission->score !== null && $submission->total > 0)
                    ? round(($submission->score / $submission->total) * 100, 1)
                    : null;
                $data['last_passed'] = ($data['last_percent'] >= $this->pass_percent);
                $data['status'] = $submission->score === null
                    ? 'pending_review'
                    : ($data['last_passed'] ? 'passed' : 'failed');

                $data['attempts'][] = [
                    'id' => $submission->id,
                    'score' => $submission->score,
                    'percent' => $data['last_percent'],
                    'is_submitted' => $submission->is_submitted,
                    'is_checked' => $submission->is_checked,
                    'submitted_at' => date('Y-m-d H:i:s', $submission->created_at),
                ];
                $data['total_attempts'] = 1;
            } else {
                $data['status'] = 'not_attempted';
            }
        }

        return $data;
    }

}
