<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\QuizQuestion $model */
/** @var common\models\Quiz $quiz */

$this->title = $model->isNewRecord ? Yii::t('app', 'Savol qo‘shish') : Yii::t('app', 'Savolni tahrirlash');
?>

<div class="container py-4">
    <h4 class="mb-4"><?= Html::encode($this->title) ?>: <?= Html::encode($quiz->title) ?></h4>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 4]) ?>

    <?= $form->field($model, 'explanation')->textarea(['rows' => 2]) ?>

    <?= $form->field($model, 'order')->textInput(['type' => 'number']) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Bekor qilish'), ['question-manage', 'quiz_id' => $quiz->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
