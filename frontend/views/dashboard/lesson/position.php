<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models common\models\Lesson[] */
/** @var common\models\Lesson $model */

$courseId = Yii::$app->request->get('course_id');

$this->title = Yii::t('app', 'Darslar ro‘yxatini tartibga solish');

$this->registerCssFile('//cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.css');
$this->registerJsFile('//cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.js', ['depends' => \yii\web\JqueryAsset::class]);
$this->registerJs(<<<JS
    $('#nestable-lesson').nestable();

    $('#save-order').on('click', function () {
        let order = $('#nestable-lesson').nestable('serialize');
        $.ajax({
            url: 'update-position?model=lesson',
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
            <?= Html::a(Yii::t('app', 'Yangi qo‘shish'), ['dashboard/lesson-create', 'course_id' => $courseId], [
                'class' => 'btn btn-primary text-nowrap mb-4'
            ]) ?>

            <?= Html::a(Yii::t('app', 'Ro‘yxatga qaytish'), ['dashboard/lesson-index', 'course_id' => $courseId], [
                'class' => 'btn btn-secondary text-nowrap mb-4'
            ]) ?>

            <div class="dd" id="nestable-lesson">
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