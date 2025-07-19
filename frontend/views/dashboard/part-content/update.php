<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\PartContent $model */
/** @var common\models\Part $part */
/** @var object|null $contentModel */

$this->title = Yii::t('app', 'Kontentni tahrirlash');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bo‘limlar'), 'url' => ['part-index', 'lesson_id' => $part->lesson_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-4">
    <?php $form = ActiveForm::begin([
        'id' => 'part-content-form',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'type_id')->dropDownList(
                [$model->type_id => $model->type->label],
                ['disabled' => true]
            ) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'position')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropDownList([
                1 => Yii::t('app', 'Aktiv'),
                0 => Yii::t('app', 'Noaktiv'),
            ]) ?>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'part_id') ?>

    <div class="mt-4">
        <?php if ($contentModel): ?>
            <?= $this->render('//dashboard/part-content/forms/_form_' . $model->type->name, ['model' => $contentModel]) ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <?= Yii::t('app', 'Ushbu kontentga mos model topilmadi.') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group mt-4">
        <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Bekor qilish'), ['dashboard/part-content-index', 'part_id' => $model->part_id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php if ($model->type->name === 'picture' && $contentModel->file_path): ?>
        <hr>
        <div class="mt-3">
            <label><?= Yii::t('app', 'Yuklangan rasm:') ?></label><br>
            <img src="<?= $contentModel->file_path ?>" class="img-thumbnail" style="max-width: 300px;">
        </div>
    <?php endif; ?>

    <?php if ($model->type->name === 'pdf' && $contentModel->file_path): ?>
        <hr>
        <div class="mt-3">
            <label><?= Yii::t('app', 'Yuklangan PDF:') ?></label><br>
            <a href="<?= $contentModel->file_path ?>" target="_blank" class="btn btn-outline-primary">
                <?= Yii::t('app', 'PDF ni ochish') ?>
            </a>
        </div>
    <?php endif; ?>
</div>
