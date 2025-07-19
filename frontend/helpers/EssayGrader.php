<?php
namespace api\helpers;

use common\models\EssayCriteria;
use Yii;

class EssayGrader
{
    public static function evaluate($quiz_id, $answerText): array
    {
        $criteria = EssayCriteria::find()->where(['quiz_id' => $quiz_id])->all();
        $scores = [];
        $totalScore = 0;

        foreach ($criteria as $item) {
            // 👇 Bu yerda siz haqiqiy AI API chaqirishingiz kerak
            $score = self::fakeScore($answerText, $item->title); // vaqtincha

            $scores[$item->title] = $score;
            $totalScore += $score * ($item->weight / 100);
        }

        return [
            'criteria_scores' => json_encode($scores, JSON_UNESCAPED_UNICODE),
            'total_score' => round($totalScore, 2),
            'feedback' => self::generateFeedback($scores),
        ];
    }

    private static function fakeScore($text, $criterion)
    {
        // Random ball – test uchun
        return rand(6, 10);
    }

    private static function generateFeedback($scores)
    {
        $lines = [];
        foreach ($scores as $k => $v) {
            $lines[] = "$k: $v/10";
        }
        return implode("\n", $lines);
    }
}
