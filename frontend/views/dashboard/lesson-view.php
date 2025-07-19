<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \common\models\Lesson $lesson */
/** @var \common\models\Part[] $parts */
/** @var array $completedPartIds */
/** @var bool $isLessonFullyCompleted */


$this->title = $lesson->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kurslar'), 'url' => ['courses']];
$this->params['breadcrumbs'][] = $lesson->title;

$courseUrl = Url::to(['/dashboard/course/view/'.$lesson->course_id]);
$loadPartUrl = Url::to(['/dashboard/load-part']);
$markCompleteUrl = Url::to(['/dashboard/mark-part-complete']);
$csrf = Yii::$app->request->csrfToken;


$quizSummaryText = '';
if (!empty($quizAttempts)) {
    $scores = [];
    foreach ($quizAttempts as $data) {
        $title = $data['title'] ?? 'Test';
        $lastScore = $data['last_score'] ?? 0;
        $totalQuestions = $data['total_questions'] ?? 0;
        $percent = $data['last_percent'] ?? 0;

        $scores[] = "{$title}: {$percent}% ({$lastScore}/100)";
    }
    $quizSummaryText = 'Test ballari: ' . implode(', ', $scores);
} else {
    $quizSummaryText = 'Test mavjud emas';
}



// Keyingi boshlanadigan partni aniqlash
$nextPartId = null;
foreach ($parts as $part) {
    if (!in_array($part->id, $completedPartIds)) {
        $nextPartId = $part->id;
        break;
    }
}
?>
    <div class="card overflow-hidden invoice-application" style="min-height: 100%; position: relative;">
        <!-- Mobile Sidebar: faqat kichik ekranlar uchun (d-lg-none) -->
        <div class="d-block d-lg-none border-bottom">
            <button class="btn btn-light w-100 text-start py-3" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebar" aria-expanded="false" aria-controls="mobileSidebar">
                <i class="ti ti-menu-2 me-2"></i> 📚 Bo‘limlar ro‘yxati
            </button>
            <div class="collapse" id="mobileSidebar">
                <ul class="list-group">
                    <?php foreach ($parts as $part): ?>
                        <li class="list-group-item <?= in_array($part->id, $completedPartIds) ? 'bg-light-subtle' : '' ?>">
                            <a href="#" class="load-part-btn d-flex align-items-start text-decoration-none text-dark" data-id="<?= $part->id ?>">
                                <div class="btn btn-sm btn-primary rounded-circle me-2">
                                    <i class="ti ti-book fs-6"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong><?= Html::encode($part->title) ?></strong>
                                    <?php if ($part->test): ?>
                                        <div class="text-muted small">Test mavjud</div>
                                    <?php endif; ?>
                                    <?php if (in_array($part->id, $completedPartIds)): ?>
                                        <span class="badge bg-success mt-1">Tugallangan</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-stretch">
            <!-- Sidebar -->
            <div class="w-25 d-none d-lg-block border-end user-chat-box" style="overflow-y: auto;">
                <div class="app-invoice">
                    <ul class="invoice-users">
                        <?php $icon = 0; ?>
                        <?php foreach ($parts as $part): ?>
                            <li>
                                <a href="#" class="p-3 bg-hover-light-black border-bottom d-flex align-items-start invoice-user listing-user load-part-btn <?= in_array($part->id, $completedPartIds) ? 'bg-light-subtle' : '' ?>" data-id="<?= $part->id ?>">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 50px;">
                                        <?php if ($icon === 0): ?>
                                            <img src="/template/assets/images/backgrounds/welcome-bg.png" alt="Goal" style="width: 40px; height: 50px; object-fit: contain;">
                                        <?php elseif ($icon === 1): ?>
                                            <img src="/template/assets/images/backgrounds/track-bg.png" alt="Track" style="width: 40px; height: 50px; object-fit: contain;">
                                        <?php else: ?>
                                            <div class="btn btn-primary round rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="ti ti-book fs-6 text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ms-3 d-inline-block w-75">
                                        <h6 class="mb-0"><?= Html::encode($part->title) ?></h6>
                                        <?php if ($part->test): ?>
                                            <span class="fs-3 text-body-color d-block">Test mavjud</span>
                                        <?php endif; ?>
                                        <?php if (in_array($part->id, $completedPartIds)): ?>
                                            <span class="badge bg-success mt-1">Tugallangan</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                            <?php $icon++; ?>
                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>

            <!-- Content -->
            <div class="w-100 w-md-75 chat-container">
                <div class="invoice-inner-part h-100">
                    <div class="invoiceing-box p-4" id="part-content-container">
                        <div id="lesson-top"></div>
                        <div id="part-loading" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                            <div class="mt-2">Bo‘lim yuklanmoqda...</div>
                        </div>
                        <div id="part-html-wrapper" class="part-ajax-wrapper">
                            <!-- 🔵 Radial Progress + Partlar Ro‘yxati -->
                            <div class="row g-4 align-items-start my-4">
                                <!-- Chart chapda yoki yuqorida -->
                                <div class="col-12 col-md-5">
                                    <div class="text-center" style="max-width: 300px; margin: 0 auto;">
                                        <canvas id="lessonProgressChart"></canvas>
                                    </div>
                                </div>

                                <!-- Partlar holati ro‘yxati -->
                                <div class="col-12 col-md-7">
                                    <h5 class="mb-3">
                                        <i class="ti ti-clipboard-list me-2 text-primary"></i> Bo‘limlar holati
                                    </h5>

                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($parts as $part): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <span><?= Html::encode($part['title']) ?></span>
                                                <?php if (in_array($part['id'], $completedPartIds)): ?>
                                                    <span class="text-success"><i class="ti ti-check-circle fs-5"></i></span>
                                                <?php else: ?>
                                                    <span class="text-muted"><i class="ti ti-dots-circle-horizontal fs-5"></i></span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>

                            <?php if ($isLessonFullyCompleted): ?>
                                <div class="text-center mt-4">
                                    <div class="alert alert-success d-inline-block px-4 py-2">
                                        <i class="ti ti-check-circle me-1"></i> Ushbu dars to‘liq yakunlandi!
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($quizAttempts)): ?>
                                <div class="mt-4">
                                    <div class="container-fluid px-0">
                                        <div class="w-100">
                                            <h6 class="fw-bold mb-3">
                                                <i class="ti ti-clipboard-check me-1"></i> Yakuniy baholash natijalari:
                                            </h6>

                                            <div class="row g-3">
                                                <?php foreach ($quizAttempts as $attempt): ?>
                                                    <?php
                                                    $score = $attempt['last_score'] ?? 0;
                                                    $percent = $attempt['last_percent'] ?? 0;
                                                    $total = 100;
                                                    $status = $attempt['status'] ?? 'unknown';
                                                    $type = $attempt['type'] ?? 'test';
                                                    $title = $attempt['title'] ?? 'Noma’lum';
                                                    $tries = $attempt['total_attempts'] ?? 0;

                                                    switch ($status) {
                                                        case 'passed':
                                                            $badgeClass = 'success';
                                                            $statusIcon = 'ti ti-circle-check';
                                                            $statusText = 'Muvaffaqqiyatli';
                                                            break;
                                                        case 'failed':
                                                            $badgeClass = 'danger';
                                                            $statusIcon = 'ti ti-circle-x';
                                                            $statusText = 'Muvaffaqqiyatsizlik';
                                                            break;
                                                        case 'pending_review':
                                                            $badgeClass = 'warning';
                                                            $statusIcon = 'ti ti-hourglass-high';
                                                            $statusText = 'Tekshirilyapti';
                                                            break;
                                                        case 'not_attempted':
                                                        case 'not_submitted':
                                                            $badgeClass = 'secondary';
                                                            $statusIcon = 'ti ti-square';
                                                            $statusText = 'Topshirilmagan';
                                                            break;
                                                        default:
                                                            $badgeClass = 'dark';
                                                            $statusIcon = 'ti ti-help-circle';
                                                            $statusText = 'Noma’lum';
                                                    }
                                                    ?>
                                                    <div class="col-12 col-md-6 col-lg-6">
                                                        <div class="border rounded bg-<?= $badgeClass ?> bg-opacity-10 p-3 h-100">
                                                            <div class="d-flex align-items-start">
                                                                <div class="me-3">
                                                                    <i class="<?= $statusIcon ?> display-6 text-<?= $badgeClass ?>"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1 text-dark fw-bold"><?= Html::encode($title) ?> <small class="text-muted">(<?= $type === \common\models\Quiz::TYPE_ESSAY ? 'Ese' : 'Test' ?>)</small></h6>
                                                                    <div class="text-dark">
                                                                        <strong>Ball:</strong> <?= $score ?>/<?= $total ?>
                                                                    </div>
                                                                    <div class="text-dark">
                                                                        <strong>Status:</strong> <?= $statusText ?>
                                                                    </div>
                                                                    <div class="text-dark">
                                                                        <strong>Urinishlar:</strong> <?= $tries ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>


                            <?php if ($nextPartId): ?>
                                <div class="text-center mt-4">
                                    <div>
                                        <?= Html::encode($parts[array_search($nextPartId, array_column($parts, 'id'))]->title) ?>
                                    </div>
                                    <button type="button" class="btn btn-lg btn-success px-5 py-2 continue-reading-btn"
                                            data-part-id="<?= $nextPartId ?>">
                                        <i class="ti ti-player-play fs-5 me-1"></i> O‘qishni davom etish
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="text-center mt-4">
                                    <div class="alert alert-success d-inline-block px-4 py-2">
                                        <i class="ti ti-check-circle me-1"></i> Barcha bo‘limlar yakunlandi!
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
const courseUrl = '$courseUrl'; 
function loadPart(partId, index = 0) {
    const loadingEl = document.getElementById('part-loading');
    const htmlWrapper = document.getElementById('part-html-wrapper');

    loadingEl.classList.remove('d-none');
    htmlWrapper.innerHTML = '';

    fetch('$loadPartUrl?id=' + partId + '&index=' + index, {
        headers: {
            'X-CSRF-Token': '$csrf'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Server bilan bog‘lanib bo‘lmadi.');
        return res.json();
    })
    .then(data => {
        loadingEl.classList.add('d-none');
        if (data.success) {
            htmlWrapper.innerHTML = data.html || '<div class="alert alert-info text-center">Kontent mavjud emas.</div>';
        } else {
            htmlWrapper.innerHTML = '<div class="alert alert-danger">Xatolik: ' + (data.message || 'Yuklanmadi') + '</div>';
        }
    })
    .catch(error => {
        loadingEl.classList.add('d-none');
        htmlWrapper.innerHTML = '<div class="alert alert-danger">Xatolik yuz berdi: ' + error.message + '</div>';
    });
}

document.addEventListener('click', function(e) {
    const loadBtn = e.target.closest('.load-part-btn');
    if (loadBtn) {
        e.preventDefault();
        const partId = loadBtn.getAttribute('data-id');
        loadPart(partId, 0);
    }

    const navBtn = e.target.closest('.part-nav-btn');
    if (navBtn) {
        const index = navBtn.getAttribute('data-index');
        const partId = navBtn.getAttribute('data-part-id');
        loadPart(partId, index);
    }
    const completeBtn = e.target.closest('.part-complete-next');
    if (completeBtn) {
        const partId = completeBtn.getAttribute('data-part-id');
        const nextId = completeBtn.getAttribute('data-next-id');
        const index = completeBtn.getAttribute('data-index');
    
        fetch('$markCompleteUrl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '$csrf'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ part_id: partId, index: index ? parseInt(index) : null })
        })


        .then(res => {
            if (!res.ok) {
                return res.json().then(err => {
                    throw new Error(err.message || 'Serverdan noto‘g‘ri javob');
                }).catch(() => {
                    throw new Error('Serverdan noto‘g‘ri javob');
                });
            }
            return res.json();
        })

        .then(data => {
            if (!data.success) {
                console.warn('MarkPartComplete javobi:', data);
        
                if (data.refresh) {
                    location.reload(); // sahifa yangilansa Flash alert chiqadi
                } else {
                    alert(data.message || 'Bo‘limni yakunlashda muammo');
                }
        
                return;
            }
        
            // ✅ Frontda "Tugallangan" belgisi qo‘shish
            const partBtns = document.querySelectorAll('.load-part-btn[data-id="' + partId + '"]');
                partBtns.forEach(partBtn => {
                    partBtn.classList.add('bg-light-subtle');
                    const badgeContainer = partBtn.querySelector('.ms-3');
                    if (badgeContainer && !badgeContainer.querySelector('.badge')) {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-success mt-1';
                        badge.innerText = 'Tugallangan';
                        badgeContainer.appendChild(badge);
                    }
                });
            
                if (nextId) {
                    loadPart(nextId, 0);
                } else {
                // Barcha bo‘limlar tugagan bo‘lsa, chiroyli UI ko‘rsatamiz
                const htmlWrapper = document.getElementById('part-html-wrapper');
                htmlWrapper.innerHTML = `
                    <div class="text-center py-5">
                        <div class="alert alert-success d-inline-block text-center p-5 shadow-lg border-0 rounded-4" style="max-width: 500px;">
                            <div class="mb-3">
                                <i class="ti ti-checkup-list display-4 text-success"></i>
                            </div>
                            <h4 class="mb-2">🎉 Barcha bo‘limlar yakunlandi!</h4>
                            <p class="mb-2 fw-bold">Dars nomi: {$lesson->title}</p>
                            <p class="mb-2">{$quizSummaryText}</p>
                            <a href="` + courseUrl + `" class="btn btn-lg btn-outline-success rounded-pill px-4">
                                <i class="ti ti-arrow-left me-1"></i> Kursga qaytish
                            </a>
                        </div>
                    </div>
                `;



            }
        })

        .catch(error => {
            alert('Bo‘limni yakunlashda xatolik yuz berdi: ' + error.message);
        });
    }

});
JS;

$this->registerJs($js);
?>
<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);


// Total and Completed bo‘limlarni hisoblash
$completedPartIds = array_filter($completedPartIds, function ($partId) use ($parts) {
    return in_array($partId, array_column($parts, 'id'));
});
$completed = count($completedPartIds);
$total = count($parts);
$percentage = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
$chartJs = <<<JS
const ctx = document.getElementById('lessonProgressChart').getContext('2d');

// Gradient yaratish
let gradient = ctx.createLinearGradient(0, 0, 0, 200);
gradient.addColorStop(0, '#00c6ff');
gradient.addColorStop(1, '#0072ff');

const completed = $completed;
const total = $total;
const percentage = $percentage;
const remaining = 100 - percentage;

// Chart ma'lumotlari
const data = {
    labels: ['Tugallangan', 'Qolgan'],
    datasets: [{
        data: [percentage, remaining],
        backgroundColor: [gradient, '#f1f1f1'],
        borderWidth: 6,
        cutout: '75%',
        borderRadius: 15,
        hoverOffset: 4
    }]
};

// Markaziy matn plugin
const centerTextPlugin = {
    id: 'centerText',
    beforeDraw(chart) {
        const { width, height, ctx } = chart;
        ctx.restore();
        const fontSize = (height / 6).toFixed(2);
        ctx.font = fontSize + "px Arial";
        ctx.fillStyle = "#111";
        ctx.textBaseline = "middle";
        const text = `{$completed}/{$total}`;
        const textX = Math.round((width - ctx.measureText(text).width) / 2);
        const textY = height / 2;
        ctx.fillText(text, textX, textY);
        ctx.save();
    }
};

// Sozlamalar
const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return context.label + ": " + context.parsed + "%";
                }
            }
        }
    }
};

// Grafik chizish
new Chart(ctx, {
    type: 'doughnut',
    data: data,
    options: options,
    plugins: [centerTextPlugin]
});
JS;

$this->registerJs($chartJs);
?>
