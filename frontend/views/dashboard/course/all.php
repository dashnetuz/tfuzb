<?php
use yii\helpers\Html;

/* @var $models common\models\Course[] */
/* @var $this yii\web\View */
?>
<div class="datatables">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-nowrap align-middle w-100" style="table-layout: fixed;">
                    <thead>
                    <tr>
                        <th style="width: 10%;">#ID</th>
                        <th style="width: 20%;"><?= Yii::t('app', 'Category ID') ?></th>
                        <th style="width: 20%;"><?= Yii::t('app', 'Rasm va nomi') ?></th>
                        <th class="d-none d-md-table-cell" style="width: 25%;"><?= Yii::t('app', 'Tavsif') ?></th>
                        <th class="d-none d-md-table-cell" style="width: 25%;"><?= Yii::t('app', 'URL') ?></th>
                        <th class="d-none d-md-table-cell" style="width: 10%;"><?= Yii::t('app', 'Faollik') ?></th>
                        <th style="width: 10%;"><?= Yii::t('app', 'Amallar') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($models as $item): ?>
                        <tr>
                            <td class="text-wrap" style="word-break: break-word;">#<?= $item->id ?></td>
                            <td class="text-wrap" style="word-break: break-word;"><?= $item->category_id ?></td>

                            <td class="text-wrap" style="word-break: break-word;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <img src="/<?= $item->picture ?: 'template/assets/images/profile/user-1.jpg' ?>" width="45"/>
                                    <div>
                                        <strong><?= Html::encode($item->title_uz) ?></strong>
                                    </div>
                                </div>
                            </td>

                            <td class="d-none d-md-table-cell text-wrap" style="word-break: break-word;">
                                <?= Html::encode($item->description_uz) ?>
                            </td>

                            <td class="d-none d-md-table-cell text-wrap" style="word-break: break-word;">
                                <small><b>UZ:</b> <?= Html::encode($item->url_uz) ?></small><br>
                                <small><b>RU:</b> <?= Html::encode($item->url_ru) ?></small><br>
                                <small><b>EN:</b> <?= Html::encode($item->url_en) ?></small>
                            </td>

                            <td class="d-none d-md-table-cell text-wrap" style="word-break: break-word;">
                                <span class="badge bg-<?= $item->is_active ? 'success' : 'danger' ?>">
                                    <?= $item->is_active ? Yii::t('app', 'Active') : Yii::t('app', 'Draw') ?>
                                </span>
                            </td>

                            <td class="text-center text-wrap" style="word-break: break-word;">
                                <div class="action-btn d-flex justify-content-center gap-2">
                                    <?= Html::a('<i class="ti ti-menu fs-5"></i>', ['dashboard/lesson-index', 'course_id' => $item->id], [
                                        'class' => 'text-info', 'title' => Yii::t('app', 'Kurslar ro‘yxati')
                                    ]) ?>
                                    <?= Html::a('<i class="ti ti-pencil fs-5"></i>', ['dashboard/course-update', 'id' => $item->id], [
                                        'class' => 'text-primary', 'title' => Yii::t('app', 'Tahrirlash')
                                    ]) ?>
                                    <?= Html::a('<i class="ti ti-trash fs-5"></i>', ['dashboard/course-delete', 'id' => $item->id], [
                                        'class' => 'text-danger', 'title' => Yii::t('app', 'O‘chirish'),
                                        'data' => ['confirm' => Yii::t('app', 'Haqiqatan o‘chirishni istaysizmi?'), 'method' => 'post']
                                    ]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="d-none d-md-table-row-group">
                    <tr>
                        <th>#</th>
                        <th style="width: 20%;"><?= Yii::t('app', 'Category ID') ?></th>
                        <th><?= Yii::t('app', 'Rasm va nomi') ?></th>
                        <th><?= Yii::t('app', 'Tavsif') ?></th>
                        <th><?= Yii::t('app', 'URL') ?></th>
                        <th><?= Yii::t('app', 'Faollik') ?></th>
                        <th><?= Yii::t('app', 'Amallar') ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
