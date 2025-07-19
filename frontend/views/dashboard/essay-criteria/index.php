<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Quiz $quiz */
/** @var common\models\EssayCriteria[] $criteriaList */

$this->title = Yii::t('app', 'Essay mezonlari');
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><?= Html::encode($this->title) ?>: <?= Html::encode($quiz->title) ?></h4>
        <div>
            <?= Html::a('<i class="ti ti-arrow-left"></i> Orqaga', ['quiz-index', 'part_id' => $quiz->part_id], ['class' => 'btn btn-light']) ?>
            <?= Html::a('<i class="ti ti-plus"></i> Yangi mezon', ['essay-criteria-create', 'quiz_id' => $quiz->id], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="list-group">
        <?php foreach ($criteriaList as $criteria): ?>
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <strong><?= Html::encode($criteria->title) ?></strong>
                    <div class="text-muted small"><?= Html::encode($criteria->description) ?></div>
                    <span class="badge bg-primary mt-1"><?= $criteria->weight ?>%</span>
                </div>
                <div class="text-end">
                    <?= Html::a('Tahrirlash', ['essay-criteria-update', 'id' => $criteria->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    <?= Html::a('<i class="ti ti-trash"></i>', ['essay-criteria-delete', 'id' => $criteria->id], [
                        'class' => 'btn btn-outline-danger btn-sm',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('app', 'Haqiqatan ham o‘chirmoqchimisiz?')
                    ]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
