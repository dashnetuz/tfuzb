<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var \common\models\Setting $model */

$this->title = Yii::t('app', 'Sayt Sozlamalari');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg">
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]); ?>

                    <!-- Tillar bo'yicha tablar -->
                    <ul class="nav nav-tabs mb-3" id="langTabs" role="tablist">
                        <?php foreach (['' => 'O‘zbekcha', '_ru' => 'Русский', '_en' => 'English'] as $suff => $label): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $suff === '' ? 'active' : '' ?>"
                                        id="tab<?= $suff ?: 'uz' ?>" data-bs-toggle="tab"
                                        data-bs-target="#pane<?= $suff ?: 'uz' ?>" type="button"
                                        role="tab" aria-selected="<?= $suff === '' ? 'true' : 'false' ?>">
                                    <?= $label ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="tab-content mb-4">
                        <?php foreach (['' => 'O‘zbekcha', '_ru' => 'Русский', '_en' => 'English'] as $suff => $label): ?>
                            <div class="tab-pane fade <?= $suff === '' ? 'show active' : '' ?>" id="pane<?= $suff ?: 'uz' ?>" role="tabpanel">
                                <div class="row">
                                    <?php foreach (['title', 'addres', 'copyright', 'description'] as $field): ?>
                                        <div class="col-md-6">
                                            <?= $form->field($model, $field . $suff, [
                                                'template' => '<div class="form-floating mb-3">{input}{label}{error}</div>'
                                            ])->{$field === 'description' ? 'textarea' : 'textInput'}([
                                                'placeholder' => $model->getAttributeLabel($field . $suff),
                                                'style' => $field === 'description' ? 'height: 100px' : null,
                                                'class' => 'form-control' . ($model->hasErrors($field . $suff) ? ' is-invalid' : ' is-valid')
                                            ]) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <!-- Qolgan oddiy maydonlar -->
                    <div class="row">
                        <?php foreach (['mail', 'tell', 'facebook', 'instagram', 'telegram', 'youtube'] as $attr): ?>
                            <div class="col-md-6">
                                <?= $form->field($model, $attr, [
                                    'template' => '<div class="form-floating mb-3">{input}{label}{error}</div>'
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel($attr),
                                    'class' => 'form-control' . ($model->hasErrors($attr) ? ' is-invalid' : ' is-valid'),
                                    'id' => $attr === 'tell' ? 'inputPhone' : null
                                ]) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <!-- Fayl yuklash -->
                    <div class="row">
                        <?php
                        $uploads = [
                            'logo1' => 'logo',
                            'logo_bottom1' => 'logo_bottom',
                            'favicon1' => 'favicon',
                            'open_graph_photo1' => 'open_graph_photo'
                        ];
                        ?>
                        <?php foreach ($uploads as $uploadField => $previewField): ?>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, $uploadField)->fileInput(['class' => 'form-control']) ?>
                                <?php if (!empty($model->$previewField)): ?>
                                    <img src="<?= Html::encode($model->$previewField) ?>" class="img-thumbnail mt-2" width="120">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-end mt-4 gap-3">
                        <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-success']) ?>
                        <?= Html::resetButton(Yii::t('app', 'Bekor qilish'), ['class' => 'btn btn-outline-danger']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs(<<<JS
  $('#inputPhone').mask('+998 (00) 000-00-00');
JS);
?>
