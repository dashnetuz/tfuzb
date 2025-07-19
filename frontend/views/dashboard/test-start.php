<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Quiz $quiz */
/** @var common\models\QuizQuestion[] $questions */

$this->title = Html::encode($quiz->title);
?>

<div class="container mt-4 quiz-container">

    <?php if ($quiz->description): ?>
        <div class="alert alert-info"><?= Yii::$app->formatter->asNtext($quiz->description) ?></div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>

    <?php foreach ($questions as $index => $question): ?>
        <div class="card mb-4">
            <div class="card-header">
                <?= ($index + 1) . '. ' . Html::encode($question->body) ?>
            </div>
            <div class="card-body">
                <?php foreach ($question->options as $option): ?>
                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="question[<?= $question->id ?>]"
                            value="<?= $option->id ?>"
                            id="q<?= $question->id ?>_<?= $option->id ?>"
                        >
                        <label class="form-check-label" for="q<?= $question->id ?>_<?= $option->id ?>">
                            <?= Html::encode($option->body) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="text-center mt-4">
        <?= Html::submitButton(Yii::t('app', 'Yakunlash va natijani ko‘rish'), ['class' => 'btn btn-success btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
