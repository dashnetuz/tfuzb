<?php
use yii\helpers\Html;

/**
 * @var \common\models\Part $part
 * @var array $contents
 * @var int $currentIndex
 * @var bool $isCompleted
 * @var string|null $quizStatus
 * @var string|null $essayStatus
 * @var array|null $attemptInfo
 * @var int|null $nextPartId
 */

$current = $contents[$currentIndex] ?? null;
$contentCount = count($contents);
?>

<div class="part-content" id="part-<?= $part->id ?>">

    <div class="d-flex justify-content-between align-items-center border-bottom mb-3 pb-2">
        <h2 class="mb-0"><strong><?= Html::encode($part->title) ?></strong></h2>
        <?php
        $totalCount = count($contents);
        $progressCount = $currentIndex + 1;
        ?>

        <?php if ($isCompleted): ?>
            <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Tugallangan</span>
        <?php else: ?>
            <div class="d-flex align-items-center gap-2">
                <div class="progress" style="width: 120px; height: 12px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($progressCount / $totalCount) * 100 ?>%;" aria-valuenow="<?= $progressCount ?>" aria-valuemin="0" aria-valuemax="<?= $totalCount ?>"></div>
                </div>
                <span class="small text-muted"><?= $progressCount ?>/<?= $totalCount ?></span>
            </div>
        <?php endif; ?>


    </div>

    <?php if ($currentIndex === 0 && $part->getDescription()): ?>
        <div class="mt-2">
            <?= $part->getDescription() ?>
        </div>
    <?php endif; ?>

    <?php if ($current): ?>
        <?= $this->render('_part_content', ['content' => $current]) ?>
    <?php else: ?>
        <div class="alert alert-warning">Kontent mavjud emas.</div>
    <?php endif; ?>

    <!-- ✅ Quiz / Essay holati -->
    <div class="completion-message mt-4">
        <?php if ($part->test): ?>
            <?php if ($quizStatus === 'not_attempted'): ?>
                <?= Html::a('Testni boshlash', ['/dashboard/test-start', 'id' => $part->test->id], ['class' => 'btn btn-primary']) ?>
            <?php elseif ($quizStatus === 'failed'): ?>
                <div class="alert alert-warning">Siz testdan o‘tolmadingiz.</div>
                <?= Html::a('Qayta urinib ko‘rish', ['/dashboard/test-start', 'id' => $part->test->id], ['class' => 'btn btn-warning']) ?>
            <?php elseif ($quizStatus === 'passed'): ?>
                <div class="text-success mb-2">
                    <i class="bi bi-check-circle-fill fs-2"></i><br>
                    <strong>Testdan muvaffaqiyatli o‘tdingiz!</strong>
                </div>
                <?php if ($attemptInfo): ?>
                    <div class="mb-2">
                        <span class="badge bg-info">Natija: <?= $attemptInfo['score'] ?>%</span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($part->essay): ?>
            <?php if ($essayStatus === 'submitted'): ?>
                <div class="alert alert-success mt-2">Essay topshirildi va baholandi.</div>
            <?php elseif ($essayStatus === 'pending'): ?>
                <div class="alert alert-warning mt-2">Sizning essayingiz tekshirilmoqda.</div>
            <?php else: ?>
                <div class="alert alert-info mt-2">Siz esse topshirishingiz kerak.</div>
                <?= Html::a('Essayni yozish', ['/dashboard/essay-submit', 'part_id' => $part->id], ['class' => 'btn btn-outline-primary']) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- ✅ Navigatsiya -->
    <div class="d-flex justify-content-between mt-4">
        <?php if ($currentIndex > 0): ?>
            <button class="btn btn-outline-secondary part-nav-btn" data-index="<?= $currentIndex - 1 ?>" data-part-id="<?= $part->id ?>">
                <i class="bi bi-arrow-left"></i> Orqaga
            </button>
        <?php else: ?>
            <div></div>
        <?php endif; ?>

        <?php if ($currentIndex < $contentCount - 1): ?>
            <button class="btn btn-primary part-nav-btn" data-index="<?= $currentIndex + 1 ?>" data-part-id="<?= $part->id ?>">
                Keyingisi <i class="ti ti-arrow-right"></i>
            </button>
        <?php else: ?>
            <button class="btn btn-success part-complete-next"
                    data-part-id="<?= $part->id ?>"
                    data-next-id="<?= $nextPartId ?>"
                    data-index="<?= $currentIndex ?>">
                Keyingisi <i class="ti ti-circle-check"></i>
            </button>
        <?php endif; ?>
    </div>
</div>
