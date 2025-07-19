<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\QuizOption $model */
/** @var common\models\QuizQuestion $question */

$this->title = $model->isNewRecord ? Yii::t('app', 'Variant qo‘shish') : Yii::t('app', 'Variantni tahrirlash');
?>

<div class="container py-4">
    <h4 class="mb-4"><?= Html::encode($this->title) ?>: <?= Html::encode($question->body) ?></h4>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'body')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_correct')->checkbox()->label(Yii::t('app', 'To‘g‘ri javob')) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Bekor qilish'), ['option-manage', 'question_id' => $question->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
