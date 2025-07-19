<?php
use yii\helpers\Html;

/** @var common\models\PartContent $content */

$active = $content->activeContent;
$type = $content->type->name;
?>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <?php if ($type === 'text' && $active): ?>
            <div class="content-text">
                <?= $active->getText() ?>
            </div>

        <?php elseif ($type === 'picture' && $active): ?>
            <div class="content-picture text-center">
                <?= Html::img($active->getImageUrl(), [
                    'class' => 'img-fluid w-100',
                    'alt' => Html::encode($active->alt),
                ]) ?>

                <?php if (!empty($active->caption)): ?>
                    <p class="mt-2 text-muted"><i><?= Html::encode($active->caption) ?></i></p>
                <?php endif; ?>
            </div>

        <?php elseif ($type === 'video' && $active): ?>
            <div class="content-video ratio ratio-16x9">
                <iframe src="<?= Html::encode($active->getEmbedUrl()) ?>" frameborder="0" allowfullscreen></iframe>
            </div>

        <?php elseif ($type === 'pdf' && $active): ?>
            <div class="content-pdf">
                <iframe src="<?= Html::encode($active->getPdfUrl()) ?>" width="100%" height="600px"></iframe>
                <div class="mt-2">
                    <a href="<?= Html::encode($active->getPdfUrl()) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <?= Yii::t('app', 'PDF ni to‘liq ko‘rish') ?>
                    </a>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">
                <?= Yii::t('app', 'Mazmun topilmadi yoki nomuvofiq') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
