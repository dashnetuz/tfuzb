<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ContentType;

/** @var yii\web\View $this */
/** @var common\models\PartContent $model */
/** @var common\models\Part $part */
/** @var object|null $contentModel */

$form = ActiveForm::begin([
    'id' => 'part-content-form',
    'options' => ['enctype' => 'multipart/form-data'],
]);
?>
<?= $form->field($model, 'type_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(ContentType::find()->all(), 'id', 'label'),
    ['prompt' => Yii::t('app', 'Tanlang...'), 'id' => 'type-selector']
) ?>

<?= $form->field($model, 'position')->textInput(['type' => 'number']) ?>

<div id="dynamic-content-form">
    <?php
    if (isset($contentModel)) {
        echo $this->render('forms/_form_' . $model->type->name, ['model' => $contentModel]);
    }
    ?>
</div>

<div class="form-group mt-3">
    <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>


<?php
$this->registerJs(<<<JS
function initTinyMceEditors() {
    if (typeof tinymce !== 'undefined') {
        tinymce.remove();
    }

    tinymce.init({
        selector: '.tinymce-multilang',
        menubar: false,
        plugins: 'lists link image code fullscreen',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code fullscreen',
        height: 250,
        
        // 👇 IKKITA MUHIM QATOR — ikonalarni saqlab qoladi
        extended_valid_elements: 'i[class|style]',
        valid_elements: '*[*]',
        
        // 👇 Tashqi CSS orqali Tabler Icon’larni ko‘rsatish
        content_css: 'https://unpkg.com/@tabler/icons-webfont@latest/tabler-icons.min.css'
    });
}

// Boshlang‘ich ishga tushirish
initTinyMceEditors();

// AJAX orqali yuklaganda ham qayta ishga tushirish
$('#type-selector').on('change', function () {
    const typeId = $(this).val();
    if (!typeId) return;

    $.get('/dashboard/load-content-form-by-type', { type_id: typeId }, function (html) {
        $('#dynamic-content-form').html(html);
        initTinyMceEditors();
    });
});
JS);

?>
