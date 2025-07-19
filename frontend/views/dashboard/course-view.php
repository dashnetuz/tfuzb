<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var common\models\Course $course */
/** @var common\models\Lesson[] $lessons */
$this->title = $course->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kurslar'), 'url' => ['courses']];
$this->params['breadcrumbs'][] = $course->title;


// Foydalanuvchining o‘qiyotgan lessonini topamiz (masalan, progress modeli bilan)
$activeLesson = null;
foreach ($lessons as $lesson) {
    if ($lesson->isInProgressByUser(Yii::$app->user->id)) { // bu metod sizda bo‘lishi kerak
        $activeLesson = $lesson;
        break;
    }
}

// Qolgan darslarni topamiz (agar activeLesson bor bo‘lsa, uni tashlab yuboramiz)
$remainingLessons = array_filter($lessons, fn($l) => !$activeLesson || $l->id !== $activeLesson->id);

?>

<div class="row g-4">
    <?php if ($activeLesson): ?>
        <div class="col-md-12 col-lg-8">
            <div class="card blog position-relative overflow-hidden hover-img h-100" style="background-image: url(<?= "/".$lesson->picture ?>);">
                <div class="card-body position-relative">
                    <div class="d-flex flex-column justify-content-between h-100">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="position-relative" data-bs-toggle="tooltip" title="<?= Html::encode($lesson->title) ?>">
                                <i class="ti ti-circle-number-<?= Html::encode($lesson->position) ?> text-primary bg-white rounded-circle d-flex align-items-center justify-content-center position-absolute"
                                   style="width: 50px; height: 50px; font-size: 50px;"
                                   data-bs-toggle="tooltip"
                                   title="<?= Html::encode($lesson->title) ?>">
                                </i>
<!--                                <img src="/template/assets/images/profile/user-3.jpg" class="rounded-circle img-fluid" width="40" height="40" alt="user">-->
                            </div>
                            <span class="badge text-bg-primary fs-2 fw-semibold"><?= Yii::t('app', 'O`qish tafsiya etiladi') ?></span>
                        </div>
                        <div>
                            <a href="<?= Url::to(['/dashboard/lesson/view/'.$activeLesson->id]) ?>" class="fs-7 my-4 fw-semibold text-white d-block lh-sm text-primary">
                                <?= Html::encode($activeLesson->title) ?>:
                                <br>
                                <?= Html::encode(mb_substr(strip_tags($activeLesson->description), 0, 1000)) ?>
                            </a>
                            <div class="d-flex align-items-center gap-4">
                                <div class="d-flex align-items-center gap-2 text-white fs-3 fw-normal">
                                    <i class="ti ti-number fs-5"></i>
                                    <?= Html::encode($lesson->position) ?>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-white fs-3 fw-normal">
                                    <i class="ti ti-puzzle fs-5"></i>
                                    <?= Html::encode($lesson->getPartCount()) ?>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-white fs-3 fw-normal">
                                    <i class="ti ti-eye fs-5"></i>
                                    102
                                </div>
                                <div class="d-flex align-items-center gap-2 text-white fs-3 fw-normal">
                                    <i class="ti ti-user fs-5"></i>
                                    29
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($remainingLessons as $lesson): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card rounded-2 overflow-hidden hover-img">
                <div class="position-relative">

                    <a href="<?= Url::to(['/dashboard/lesson/view/'.$lesson->id]) ?>">
                        <img src="<?= Url::to('/' . $lesson->picture) ?>" class="card-img-top rounded-0" alt="matdash-img">
                    </a>
                    <span class="badge text-bg-light fs-2 lh-sm mb-9 me-9 py-1 px-2 fw-semibold position-absolute bottom-0 end-0">
                        <?= Html::encode($lesson->title) ?>
                    </span>

                    <i class="ti ti-circle-number-<?= Html::encode($lesson->position) ?> text-primary bg-white rounded-circle d-flex align-items-center justify-content-center position-absolute bottom-0 start-0 mb-n9 ms-9"
                       style="width: 50px; height: 50px; font-size: 50px;"
                       data-bs-toggle="tooltip"
                       title="<?= Html::encode($lesson->title) ?>">
                    </i>
<!--                    <img src="/template/assets/images/profile/user-3.jpg" alt="matdash-img" class="img-fluid rounded-circle position-absolute bottom-0 start-0 mb-n9 ms-9" width="40" height="40" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="--><?php //= Html::encode($lesson->title) ?><!--">-->
                </div>
                <div class="card-body p-4">
                    <a class="d-block my-4 fs-5 text-dark fw-semibold link-primary" href="<?= Url::to(['/dashboard/lesson/view/'.$lesson->id]) ?>">
                        <?= Html::encode(mb_substr(strip_tags($lesson->description), 0, 255)) ?>
                    </a>
                    <div class="d-flex align-items-center gap-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-number text-dark fs-5"></i>
                            <?= Html::encode($lesson->position) ?>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-puzzle text-dark fs-5"></i>
                            <?= Html::encode($lesson->getPartCount()) ?>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-eye text-dark fs-5"></i>
                            102
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-user text-dark fs-5"></i>
                            29
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

