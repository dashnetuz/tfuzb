<?php

// frontend/helpers/PositionHelper.php

namespace frontend\helpers;

use Yii;
use yii\db\ActiveRecord;

class PositionHelper
{
    /**
     * Tartibni saqlaydi (faqat `id` va `position` mavjud bo‘lsa)
     * @param string $modelClass - to‘liq namespace bilan model (e.g. \frontend\models\Category)
     * @param array $orderedItems - nestable dan kelgan massiv
     * @return array
     */
    public static function saveOrder(string $modelClass, array $orderedItems): array
    {
        if (!class_exists($modelClass)) {
            return ['success' => false, 'error' => 'Model topilmadi: ' . $modelClass];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($orderedItems as $index => $item) {
                if (!isset($item['id'])) {
                    throw new \Exception('Elementda id yo‘q');
                }

                $model = $modelClass::findOne($item['id']);
                if ($model) {
                    $model->position = $index + 1;
                    $model->save(false, ['position']);
                }
            }
            $transaction->commit();
            return ['success' => true];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
