<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var common\models\Part $part */
/** @var common\models\Quiz[] $quizzes */

$this->title = Yii::t('app', 'Testlar');
?>

<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h4 class="mb-0"><?= Html::encode($this->title) ?>: <?= Html::encode($part->title_uz) ?></h4>
        <?= Html::a('<i class="ti ti-arrow-left"></i> ' . Yii::t('app', 'Orqaga'), ['part-content/index', 'part_id' => $part->id], ['class' => 'btn btn-light']) ?>
    </div>

    <div class="mb-3">
        <?= Html::a('<i class="ti ti-plus"></i> ' . Yii::t('app', 'Test qo‘shish'), ['quiz-create', 'part_id' => $part->id], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="row">
        <?php foreach ($quizzes as $quiz): ?>
            <div class="col-md-6">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= Html::encode($quiz->title) ?></h5>
                        <p class="card-text">
                            <?= Yii::t('app', 'Turi') ?>: <?= $quiz->type == 1 ? 'Test (variantli)' : 'Insho' ?><br>
                            <?= Yii::t('app', 'Vaqt limiti') ?>: <?= $quiz->time_limit ?> daqiqa<br>
                            <?= Yii::t('app', 'O‘tish foizi') ?>: <?= $quiz->pass_percent ?>%
                        </p>
                        <div class="d-flex justify-content-between">
                            <div>
                                <?= Html::a(Yii::t('app', 'Savollar'), ['question-manage', 'quiz_id' => $quiz->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                                <?= Html::a(Yii::t('app', 'Tahrirlash'), ['quiz-update', 'id' => $quiz->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                            </div>
                            <?= Html::a('<i class="ti ti-trash"></i>', ['quiz-delete', 'id' => $quiz->id], [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('app', 'O‘chirishga ishonchingiz komilmi?'),
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
