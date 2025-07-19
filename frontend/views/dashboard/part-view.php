<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Part $part */
/** @var common\models\PartContent[] $contents */
/** @var common\models\Test|null $test */

$this->title = $part->title;
?>

<div class="container mt-4">

    <?php if (!empty($part->description)): ?>
        <div class="mb-4">
            <?= $part->getDescription() ?>
        </div>
    <?php endif; ?>

    <div class="content-section">
        <?php foreach ($contents as $content): ?>
            <?= $this->render('_part_content', ['content' => $content]) ?>
        <?php endforeach; ?>
    </div>

    <div class="mt-5 text-center">
        <?php if ($test): ?>
            <?= Html::a(Yii::t('app', 'Testni boshlash'), ['dashboard/test-start', 'id' => $test->id], [
                'class' => 'btn btn-primary btn-lg'
            ]) ?>
        <?php else: ?>
            <div class="text-success">
                <i class="bi bi-check-circle-fill fs-1"></i><br>
                <strong><?= Yii::t('app', 'Ushbu bo‘lim yakunlandi!') ?></strong>
            </div>
        <?php endif; ?>
    </div>
</div>
