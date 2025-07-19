<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\ContentType;

/** @var yii\web\View $this */
/** @var common\models\PartContent $model */
/** @var common\models\Part $part */
/** @var yii\base\Model|null $contentModel */

$this->title = Yii::t('app', 'Kontent qo‘shish');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bo‘limlar'), 'url' => ['part-index', 'lesson_id' => $part->lesson_id]];
$this->params['breadcrumbs'][] = $this->title;

$contentTypes = ContentType::find()->select(['label', 'id'])->indexBy('id')->column();
?>

<div class="container py-4">
    <?php $form = ActiveForm::begin([
        'id' => 'part-content-form',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'type_id')->dropDownList($contentTypes, [
                'prompt' => Yii::t('app', 'Kontent turini tanlang'),
                'id' => 'content-type-selector'
            ]) ?>
        </div>
        <?= Html::activeHiddenInput($model, 'position') ?>

        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList([
                1 => Yii::t('app', 'Aktiv'),
                0 => Yii::t('app', 'Noaktiv'),
            ]) ?>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'part_id') ?>

    <div id="dynamic-content-form">
        <?php
        if ($contentModel) {
            $type = $model->type->name ?? 'text';
            $viewPathPrefix = '//dashboard/part-content/forms/';

            switch ($type) {
                case 'text':
                    echo $this->render($viewPathPrefix . '_form_text', ['model' => $contentModel]);
                    break;
                case 'picture':
                    echo $this->render($viewPathPrefix . '_form_picture', ['model' => $contentModel]);
                    break;
                case 'video':
                    echo $this->render($viewPathPrefix . '_form_video', ['model' => $contentModel]);
                    break;
                case 'pdf':
                    echo $this->render($viewPathPrefix . '_form_pdf', ['model' => $contentModel]);
                    break;
            }
        }
        ?>
    </div>

    <div class="form-group mt-4">
        <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$loadFormUrl = Url::to(['dashboard/load-content-form-by-type']);
$js = <<<JS
$('#content-type-selector').on('change', function () {
    const typeId = $(this).val();
    if (!typeId) {
        $('#dynamic-content-form').html('');
        return;
    }

    $.ajax({
        url: '$loadFormUrl',
        type: 'GET',
        data: { type_id: typeId },
        success: function (data) {
            $('#dynamic-content-form').html(data);
            initTinyEditors(); // ✅ TinyMCE qayta ishga tushadi
        },
        error: function () {
            alert('Formani yuklashda xatolik yuz berdi.');
        }
    });
});
JS;

$this->registerJs($js);
?>
