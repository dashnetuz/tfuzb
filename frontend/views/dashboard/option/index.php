<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\QuizQuestion $question */
/** @var common\models\QuizOption[] $options */

$this->title = Yii::t('app', 'Variantlar');
?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h4 class="mb-0"><?= Html::encode($this->title) ?>: <?= Html::encode($question->body) ?></h4>
        <?= Html::a('<i class="ti ti-arrow-left"></i> ' . Yii::t('app', 'Orqaga'), ['question-manage', 'quiz_id' => $question->quiz_id], ['class' => 'btn btn-light']) ?>
    </div>

    <div class="mb-3">
        <?= Html::a('<i class="ti ti-plus"></i> ' . Yii::t('app', 'Variant qo‘shish'), ['option-create', 'question_id' => $question->id], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="list-group">
        <?php foreach ($options as $option): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <?= Html::encode($option->body) ?>
                    <?php if ($option->is_correct): ?>
                        <span class="badge bg-success ms-2"><?= Yii::t('app', 'To‘g‘ri javob') ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <?= Html::a(Yii::t('app', 'Tahrirlash'), ['option-update', 'id' => $option->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    <?= Html::a('<i class="ti ti-trash"></i>', ['option-delete', 'id' => $option->id], [
                        'class' => 'btn btn-sm btn-outline-danger',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('app', 'O‘chirishga ishonchingiz komilmi?'),
                    ]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
