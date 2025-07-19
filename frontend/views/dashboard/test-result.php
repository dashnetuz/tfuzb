<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\QuizAttempt $attempt */

$this->title = Yii::t('app', 'Test natijasi');
?>

<div class="container mt-5">

    <div class="card mb-4">
        <div class="card-body">
            <h4><?= Html::encode($attempt->quiz->title) ?></h4>
            <p><?= Html::encode($attempt->quiz->description) ?></p>

            <hr>
            <p><strong><?= Yii::t('app', 'Umumiy ball') ?>:</strong> <?= $attempt->score ?>%</p>
            <p>
                <strong><?= Yii::t('app', 'Holat') ?>:</strong>
                <?php if ($attempt->is_passed): ?>
                    <span class="badge bg-success"><?= Yii::t('app', 'O‘tgan') ?></span>
                <?php else: ?>
                    <span class="badge bg-danger"><?= Yii::t('app', 'O‘tolmadi') ?></span>
                <?php endif; ?>
            </p>
            <p><strong><?= Yii::t('app', 'Urinish') ?>:</strong> <?= $attempt->try_index ?></p>
            <p><strong><?= Yii::t('app', 'Boshlangan vaqt') ?>:</strong> <?= Yii::$app->formatter->asDatetime($attempt->started_at) ?></p>
            <?php if ($attempt->ended_at): ?>
                <p><strong><?= Yii::t('app', 'Yakunlangan vaqt') ?>:</strong> <?= Yii::$app->formatter->asDatetime($attempt->ended_at) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="accordion" id="questionResults">
        <?php foreach ($attempt->answers as $index => $answer): ?>
            <?php $question = $answer->question; ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="heading<?= $index ?>">
                    <button class="accordion-button <?= $answer->is_correct ? 'bg-success text-white' : 'bg-danger text-white' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="true" aria-controls="collapse<?= $index ?>">
                        <?= Yii::t('app', 'Savol') . ' #' . ($index + 1) ?>:
                        <?= Html::encode($question->body) ?>
                    </button>
                </h2>
                <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $index ?>" data-bs-parent="#questionResults">
                    <div class="accordion-body">
                        <p><strong><?= Yii::t('app', 'Sizning javobingiz') ?>:</strong>
                            <?php if ($answer->option): ?>
                                <?= Html::encode($answer->option->body) ?>
                            <?php elseif ($answer->answer_text): ?>
                                <?= Html::encode($answer->answer_text) ?>
                            <?php else: ?>
                                <em><?= Yii::t('app', 'Hech narsa tanlanmadi') ?></em>
                            <?php endif; ?>
                        </p>

                        <p>
                            <strong><?= Yii::t('app', 'To‘g‘ri javob') ?>:</strong>
                            <?php
                            $correctOption = $question->options[0] ?? null;
                            foreach ($question->options as $opt) {
                                if ($opt->is_correct) {
                                    $correctOption = $opt;
                                    break;
                                }
                            }
                            echo $correctOption ? Html::encode($correctOption->body) : Yii::t('app', 'Topilmadi');
                            ?>
                        </p>

                        <?php if (!empty($question->explanation)): ?>
                            <p><strong><?= Yii::t('app', 'Izoh') ?>:</strong> <?= Html::encode($question->explanation) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-3">
        <?= Html::a(Yii::t('app', 'Darsga qaytish'), ['dashboard/lesson-view', 'id' => $quiz->part->lesson_id], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>
