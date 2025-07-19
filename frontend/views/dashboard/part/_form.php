<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Setting;
$setting = Setting::findOne(1);

/** @var yii\web\View $this */
/** @var common\models\Part $model */

// Slugify ulash (Quill avtomatik init bo‘ladi: quill-init.js orqali)
$this->registerJs(<<<JS
    bindSlugify('#part-title_uz', '#part-url_uz');
    bindSlugify('#part-title_ru', '#part-url_ru');
    bindSlugify('#part-title_en', '#part-url_en');
JS);
?>

<!-- Quill uchun trigger -->
<div data-quill-init="editor" data-quill-hidden="hidden-description"></div>

<div class="card">
    <div class="card-body">
        <?php
            $form = ActiveForm::begin([
                'id' => 'part-content-form',
                'options' => [
                    'enctype' => 'multipart/form-data',
                    'data-quill-init' => 'editor-text',
                    'data-quill-hidden' => 'hidden-text'
                ],
            ]);
        ?>

        <div class="card">
            <ul class="nav nav-pills user-profile-tab" role="tablist">
                <?php foreach (['uz' => 'Uz', 'ru' => 'Ru', 'en' => 'En'] as $lang => $label): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3 <?= $lang === 'uz' ? 'active' : '' ?>"
                                data-bs-toggle="pill"
                                data-bs-target="#tab-<?= $lang ?>"
                                type="button" role="tab"
                                aria-selected="<?= $lang === 'uz' ? 'true' : 'false' ?>">
                            <img src="/template/assets/images/flag/icon-flag-<?= $lang ?>.svg" alt="<?= $lang ?>" width="20" height="20" class="rounded-circle me-2 fs-6" />
                            <span class="d-none d-md-block"><?= $label ?></span>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content mt-2">
                <?php foreach (['uz', 'ru', 'en'] as $lang): ?>
                    <div class="tab-pane fade <?= $lang === 'uz' ? 'show active' : '' ?>" id="tab-<?= $lang ?>" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="card w-100 border mb-0">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <?= $form->field($model, "title_$lang")->textInput() ?>
                                                <?= $form->field($model, "url_$lang")->textInput() ?>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label"><?= $model->getAttributeLabel("description_$lang") ?></label>
                                                    <div id="editor-<?= $lang ?>" style="height: 100px;"><?= strip_tags((string) $model->{"description_$lang"}) ?></div>
                                                    <textarea id="hidden-description-<?= $lang ?>" name="Part[description_<?= $lang ?>]" style="display:none;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- is_active switch -->
        <div class="my-4 d-flex align-items-center justify-content-center">
            <span class="me-3 fw-semibold"><?= Yii::t('app', 'Draw') ?></span>
            <?= Html::hiddenInput('Part[is_active]', 0) ?>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" name="Part[is_active]" value="1" <?= $model->is_active ? 'checked' : '' ?>>
            </div>
            <span class="ms-2 fw-semibold"><?= Yii::t('app', 'Publish') ?></span>
        </div>

        <div class="form-group text-end">
            <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
