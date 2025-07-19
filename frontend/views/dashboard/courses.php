<?php
use yii\helpers\Url;
use yii\helpers\Html;

/** @var common\models\Course[] $courses */
$this->title = "Courses";
?>

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>
    .course-card {
        background: var(--card-bg, #ffffff);
        border-radius: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    body.dark .course-card {
        background: #1e293b; /* slate-800 */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    }

    .course-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        color: #fff;
        padding: 6px 14px;
        font-size: 13px;
        border-radius: 14px;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .course-img-wrapper {
        position: relative;
    }

    .course-img {
        height: 360px;
        object-fit: cover;
        border-bottom: 1px solid #eee;
        border-radius: 24px 24px 0 0;
        width: 100%;
        display: block;
    }

    .course-stats-overlay {
        position: absolute;
        bottom: -24px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.85); /* light */
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 40px;
        padding: 16px 32px;
        z-index: 3;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    body.dark .course-stats-overlay {
        background: rgba(30, 41, 59, 0.85); /* dark semi */
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .course-stats-overlay div {
        text-align: center;
        font-weight: 600;
        min-width: 80px;
        color: #1f2937; /* gray-800 light */
    }

    body.dark .course-stats-overlay div {
        color: #f9fafb; /* gray-50 dark */
    }

    .course-stats-overlay div i {
        display: block;
        font-size: 22px;
        margin-bottom: 6px;
        color: #4f46e5; /* indigo-600 light */
    }

    body.dark .course-stats-overlay div i {
        color: #818cf8; /* indigo-400 dark */
    }

    .course-stats-overlay div small {
        font-size: 13px;
        opacity: 0.9;
    }

    body.dark .course-stats-overlay div small {
        opacity: 1;
    }

    .course-title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 14px;
        color: #111827;
    }

    body.dark .course-title {
        color: #f3f4f6;
    }

    .course-desc {
        font-size: 16px;
        color: #555;
    }

    body.dark .course-desc {
        color: #d1d5db; /* text-gray-300 */
    }

    .btn-course {
        background: linear-gradient(to right, #667eea, #764ba2);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 10px 28px;
        font-size: 16px;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .btn-course:hover {
        background: linear-gradient(to right, #5a67d8, #6b46c1);
    }

    .course-card .p-5 {
        padding-top: 64px; /* extra for overlay */
    }
</style>


<div class="container my-5">
    <div class="row">
        <?php foreach ($courses as $course): ?>
            <div class="col-lg-12">
                <div class="course-card">
                    <div class="course-badge">🚀 <?= Yii::t('app', 'Yangi Kurs') ?></div>

                    <?php if ($course->picture): ?>
                        <div class="course-img-wrapper">
                            <img src="<?= Url::to($course->picture, true) ?>" class="course-img" alt="<?= Html::encode($course->title) ?>">

                            <!-- Icon-statistika rasm ustida -->
                            <div class="course-stats-overlay">
                                <div>
                                    <i class="fas fa-book-open"></i>
                                    <small><?= Yii::t('app', 'Darslar') ?></small><br>
                                    <strong><?= $course->getLessonCount() ?></strong>
                                </div>
                                <div>
                                    <i class="fas fa-puzzle-piece"></i>
                                    <small><?= Yii::t('app', 'Bo‘limlar') ?></small><br>
                                    <strong><?= $course->getPartCount() ?></strong>
                                </div>
                                <div>
                                    <i class="fas fa-video"></i>
                                    <small><?= Yii::t('app', 'Videolar') ?></small><br>
                                    <strong><?= $course->getVideoCount() ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="p-5">
                        <h2 class="course-title"><?= Html::encode($course->title) ?></h2>
                        <p class="course-desc mb-4">
                            <?= Html::encode(mb_substr(strip_tags($course->description), 0, 1000)) ?>
                        </p>
                        <a href="<?= Url::to(['/dashboard/course/view/' . $course->id]) ?>" class="btn btn-course">
                            <?= Yii::t('app', 'Batafsil ko‘rish') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
