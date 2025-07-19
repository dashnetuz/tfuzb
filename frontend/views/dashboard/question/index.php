<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Quiz $quiz */
/** @var common\models\QuizQuestion[] $questions */

$this->title = Yii::t('app', 'Savollar');
?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h4 class="mb-0"><?= Html::encode($this->title) ?>: <?= Html::encode($quiz->title) ?></h4>
        <?= Html::a('<i class="ti ti-arrow-left"></i> ' . Yii::t('app', 'Orqaga'), ['quiz-index', 'part_id' => $quiz->part_id], ['class' => 'btn btn-light']) ?>
    </div>

    <div class="mb-3">
        <?= Html::a('<i class="ti ti-plus"></i> ' . Yii::t('app', 'Savol qo‘shish'), ['question-create', 'quiz_id' => $quiz->id], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="list-group">
        <?php foreach ($questions as $question): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?= Html::encode($question->body) ?></strong>
                        <?php if ($quiz->type == 1): ?>
                            <div class="mt-2">
                                <?= Html::a(Yii::t('app', 'Variantlar'), ['option-manage', 'question_id' => $question->id], ['class' => 'btn btn-outline-info btn-sm']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?= Html::a(Yii::t('app', 'Tahrirlash'), ['question-update', 'id' => $question->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                        <?= Html::a('<i class="ti ti-trash"></i>', ['question-delete', 'id' => $question->id], [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('app', 'O‘chirishga ishonchingiz komilmi?'),
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
