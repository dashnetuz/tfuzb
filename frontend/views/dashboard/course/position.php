<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models common\models\Course[] */
/** @var common\models\Course $model */

$categoryId = Yii::$app->request->get('category_id');

$this->registerCssFile('//cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.css');
$this->registerJsFile('//cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.js', ['depends' => \yii\web\JqueryAsset::class]);
$this->registerJs(<<<JS
    $('#nestable-course').nestable();

    $('#save-order').on('click', function () {
        let order = $('#nestable-course').nestable('serialize');
        $.ajax({
            url: 'update-position?model=course',
            type: 'POST',
            data: {order: order},
            success: function (res) {
                if (res.success) {
                    toastr.success('Tartib muvaffaqiyatli saqlandi');
                } else {
                    toastr.error(res.error || 'Xatolik yuz berdi');
                }
            },
            error: function () {
                toastr.error('Server bilan bog‘lanib bo‘lmadi');
            }
        });
    });
JS);
?>

<div class="datatables">
    <div class="card">
        <div class="card-body">

            <button id="save-order" class="btn btn-success mb-4">
                <i class="ti ti-check"></i> <?= Yii::t('app', 'Tartibni saqlash') ?>
            </button>
            <?= Html::a(Yii::t('app', 'Yangi qo‘shish'), ['dashboard/course-create', 'category_id' => $categoryId], [
                'class' => 'btn btn-primary text-nowrap mb-4'
            ]) ?>

            <?= Html::a(Yii::t('app', 'Ro‘yxatga qaytish'), ['dashboard/course-index', 'category_id' => $categoryId], [
                'class' => 'btn btn-secondary text-nowrap mb-4'
            ]) ?>

            <div class="dd" id="nestable-course">
                <ol class="dd-list">
                    <?php foreach ($models as $item): ?>
                        <li class="dd-item" data-id="<?= $item->id ?>">
                            <div class="dd-handle bg-body text-body d-flex justify-content-between align-items-center border rounded p-2">
                    <span class="d-flex align-items-center">
                        <i class="ti ti-menu fs-6 me-2"></i>
                        <strong class="me-2">#<?= Html::encode($item->position) ?></strong>
                    </span>
                                <span class="d-flex align-items-center gap-2">
                        <img src="/<?= $item->picture ?: 'template/assets/images/profile/user-1.jpg' ?>" width="30" />
                        <strong><?= Html::encode($item->getTitle()) ?></strong>
                    </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>