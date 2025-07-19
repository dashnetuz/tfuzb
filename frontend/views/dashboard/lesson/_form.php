<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Setting;
$setting = Setting::findOne(1);

/** @var yii\web\View $this */
/** @var common\models\Lesson $model */

// Rasm yuklash preview
$this->registerJs(<<<JS
document.getElementById('pictureInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('picturePreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
});
JS);
$this->registerCssFile('/template/assets/libs/quill/dist/quill.snow.css', ['depends' => \yii\web\JqueryAsset::class]);

// Slugify.js ulash
$this->registerJsFile('/template/assets/js/slugify.js', ['depends' => \yii\web\JqueryAsset::class]);
$this->registerJsFile('/template/assets/libs/quill/dist/quill.min.js', ['depends' => \yii\web\JqueryAsset::class]);
$this->registerJsFile('/template/assets/js/forms/quill-init.js', ['depends' => \yii\web\JqueryAsset::class]);

// Slugify binding
$this->registerJs(<<<JS
    bindSlugify('#lesson-title_uz', '#lesson-url_uz');
    bindSlugify('#lesson-title_ru', '#lesson-url_ru');
    bindSlugify('#lesson-title_en', '#lesson-url_en');
JS);

// Quill init va submitda descriptionni textarea'ga yozish
$this->registerJs(<<<JS
    var quillUz = new Quill('#editor-uz', { theme: 'snow' });
    var quillRu = new Quill('#editor-ru', { theme: 'snow' });
    var quillEn = new Quill('#editor-en', { theme: 'snow' });

    $('form').on('submit', function () {
        $('#hidden-description-uz').val(quillUz.root.innerHTML);
        $('#hidden-description-ru').val(quillRu.root.innerHTML);
        $('#hidden-description-en').val(quillEn.root.innerHTML);
    });
JS);
?>


<div class="card">
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
        <div class="card">
            <ul class="nav nav-pills user-profile-tab" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent fs-3 py-3" id="pills-account-tab" data-bs-toggle="pill" data-bs-target="#pills-account" type="button" role="tab" aria-controls="pills-account" aria-selected="true">
                        <img src="/template/assets/images/flag/icon-flag-uz.svg"
                             alt="uz" width="20" height="20"
                             class="rounded-circle object-fit-cover round-20 me-2 fs-6" />
                        <span class="d-none d-md-block">Uz</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3" id="pills-notifications-tab" data-bs-toggle="pill" data-bs-target="#pills-notifications" type="button" role="tab" aria-controls="pills-notifications" aria-selected="false">
                        <img src="/template/assets/images/flag/icon-flag-ru.svg"
                             alt="uz" width="20" height="20"
                             class="rounded-circle object-fit-cover round-20 me-2 fs-6" />
                        <span class="d-none d-md-block">Ru</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3" id="pills-bills-tab" data-bs-toggle="pill" data-bs-target="#pills-bills" type="button" role="tab" aria-controls="pills-bills" aria-selected="false">
                        <img src="/template/assets/images/flag/icon-flag-en.svg"
                             alt="uz" width="20" height="20"
                             class="rounded-circle object-fit-cover round-20 me-2 fs-6" />
                        <span class="d-none d-md-block">En</span>
                    </button>
                </li>
            </ul>
            <div class="tab-content mt-2" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-account" role="tabpanel" aria-labelledby="pills-account-tab" tabindex="0">
                    <div class="row">

                        <div class="col-12">
                            <div class="card w-100 border position-relative overflow-hidden mb-0">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <?= $form->field($model, 'title_uz')->textInput() ?>
                                            </div>
                                            <div class="mb-3">
                                                <?= $form->field($model, 'url_uz')->textInput() ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label"><?= $model->getAttributeLabel('description_uz') ?></label>
                                                <div id="editor-uz" style="height: 100px;"><?= strip_tags((string) $model->description_uz) ?></div>
                                                <textarea id="hidden-description-uz" name="Lesson[description_uz]" style="display:none;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-notifications" role="tabpanel" aria-labelledby="pills-notifications-tab" tabindex="0">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card w-100 border position-relative overflow-hidden mb-0">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <?= $form->field($model, 'title_ru')->textInput() ?>
                                            </div>
                                            <div class="mb-3">
                                                <?= $form->field($model, 'url_ru')->textInput() ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label"><?= $model->getAttributeLabel('description_ru') ?></label>
                                                <div id="editor-ru" style="height: 100px;"><?= strip_tags((string) $model->description_ru) ?></div>
                                                <textarea id="hidden-description-ru" name="Lesson[description_ru]" style="display:none;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-bills" role="tabpanel" aria-labelledby="pills-bills-tab" tabindex="0">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card w-100 border position-relative overflow-hidden mb-0">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <?= $form->field($model, 'title_en')->textInput() ?>
                                            </div>
                                            <div class="mb-3">
                                                <?= $form->field($model, 'url_en')->textInput() ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label"><?= $model->getAttributeLabel('description_en') ?></label>
                                                <div id="editor-en" style="height: 100px;"><?= strip_tags((string) $model->description_en) ?></div>
                                                <textarea id="hidden-description-en" name="Lesson[description_en]" style="display:none;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row">
                <div class="col-lg-4">
                    <label class="form-label fw-bold"><?= Yii::t('app', 'Rasm tanlang') ?></label>
                    <input type="file" id="pictureInput" name="Lesson[picture]" class="form-control" accept="image/*">
                </div>
                <div class="col-lg-4">
                    <div class="mt-3">
                        <img id="picturePreview" src="<?= $model->picture ? '/' . $model->picture : $setting->logo ?>"
                             alt="Rasm" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>
                <div class="col-lg-4">

                    <!-- is_active switch -->
                    <div class="d-flex align-items-center justify-content-center my-4">
                        <span class="text-dark fw-bolder text-capitalize me-3"><?= Yii::t('app', 'Draw') ?></span>

                        <input type="hidden" name="Lesson[is_active]" value="0">

                        <div class="form-check form-switch mb-0">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="flexSwitchCheckChecked"
                                    name="Lesson[is_active]"
                                    value="1"
                                <?= $model->is_active == 1 ? 'checked' : '' ?>
                            >
                        </div>

                        <span class="text-dark fw-bolder text-capitalize ms-2"><?= Yii::t('app', 'Publish') ?></span>
                    </div>

                </div>
            </div>


        </div>

        <!-- Picture -->



        <!-- Submit -->
        <div class="form-group text-end">
            <?= Html::submitButton(Yii::t('app', 'Saqlash'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
