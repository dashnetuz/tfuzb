<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\PartContent $model */
/** @var common\models\ContentText|ContentPicture|ContentVideo|ContentPdf|null $content */

$this->title = Yii::t('app', 'Kontentni ko‘rish');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bo‘limlar'), 'url' => ['part-index', 'lesson_id' => $model->part->lesson_id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container py-4">
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><?= $model->type->label ?> (#<?= $model->id ?>)</h5>
            <p><strong><?= Yii::t('app', 'Holat:') ?></strong> <?= $model->status ? Yii::t('app', 'Aktiv') : Yii::t('app', 'Noaktiv') ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($content): ?>

                <?php if ($model->type->name === 'text'): ?>
                    <div class="mb-3"><?= nl2br(Html::encode($content->text_uz)) ?></div>

                <?php elseif ($model->type->name === 'picture'): ?>
                    <img src="<?= $content->file_path ?>" class="img-fluid mb-2" alt="<?= Html::encode($content->alt) ?>">
                    <?php if ($content->caption): ?>
                        <div class="text-muted"><?= Html::encode($content->caption) ?></div>
                    <?php endif; ?>

                <?php elseif ($model->type->name === 'video'): ?>
                    <div class="ratio ratio-16x9 mb-3">
                        <iframe src="https://www.youtube.com/embed/<?= getYoutubeId($content->youtube_url) ?>"
                                allowfullscreen></iframe>
                    </div>
                    <h5><?= Html::encode($content->getTitle()) ?></h5>
                    <p><?= Html::encode($content->getDescription()) ?></p>

                <?php elseif ($model->type->name === 'pdf'): ?>
                    <h5><?= Html::encode($content->getTitle()) ?></h5>
                    <p><?= Html::encode($content->getDescription()) ?></p>
                    <a href="<?= $content->file_path ?>" class="btn btn-outline-primary" target="_blank">
                        <?= Yii::t('app', 'PDF ni ochish') ?>
                    </a>

                <?php else: ?>
                    <div class="alert alert-warning"><?= Yii::t('app', 'Nomaʼlum kontent turi.') ?></div>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-warning"><?= Yii::t('app', 'Kontent topilmadi.') ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
function getYoutubeId($url) {
    preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/', $url, $matches);
    return $matches[1] ?? '';
}
?>
