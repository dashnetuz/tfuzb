<?php

use common\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Setting;
use frontend\helpers\SidebarHelper;

$activeMiniId = SidebarHelper::resolveSectionId();

$isCategory = SidebarHelper::isGroupSection('category');
$isCourse   = SidebarHelper::isGroupSection('course');
$isLesson   = SidebarHelper::isGroupSection('lesson');
$isPart     = SidebarHelper::isGroupSection('part');
$categories = Category::find()->with(['courses.lessons.parts'])->all();



$setting = Setting::findOne(1);

/**
 * Sidebar item active qilish uchun (link uchun).
 */
function isActiveDashboardItem($patterns = [], $returnType = 'active')
{
    $route = Yii::$app->controller->route ?? '';

    if (strpos($route, 'dashboard/') !== 0) {
        return '';
    }

    $subRoute = substr($route, strlen('dashboard/'));

    foreach ((array)$patterns as $pattern) {
        if (strpos($subRoute, $pattern) === 0) {
            return ($returnType === 'open') ? 'open' : 'active';
        }
    }

    // fallback: agar boshqa hech biri topilmasa, mini-1ni default ochiq qilamiz
    if (empty($patterns)) {
        return ($returnType === 'open') ? 'open' : 'active';
    }

    return '';
}

function isActiveDashboardMenu($patterns = [])
{
    $value = isActiveDashboardItem($patterns, 'open');
    return $value ?: (empty($patterns) ? 'open' : '');
}
?>
<!-- Sidebar Start -->
<aside class="side-mini-panel with-vertical">
    <div class="iconbar">
        <div>
            <div class="mini-nav">
                <div class="brand-logo d-flex align-items-center justify-content-center">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                        <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-7"></iconify-icon>
                    </a>
                </div>
                <ul class="mini-nav-ul" data-simplebar>
                    <?php if ($this->context->hasRole('user')): ?>
                        <li class="mini-nav-item <?= ($activeMiniId === 'mini-1') ? 'open' : '' ?>" id="mini-1">
                            <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Dashboards">
                                <iconify-icon icon="solar:layers-line-duotone" class="fs-7"></iconify-icon>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($this->context->hasRole('creator') || $this->context->hasRole('admin')): ?>
                        <li class="mini-nav-item <?= ($activeMiniId === 'mini-7') ? 'open' : '' ?>" id="mini-7">
                            <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Rol manager">
                                <iconify-icon icon="solar:widget-6-line-duotone" class="fs-7"></iconify-icon>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li><span class="sidebar-divider lg"></span></li>

                    <?php if ($this->context->hasRole('user')): ?>
                        <li class="mini-nav-item" id="mini-9">
                            <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Authentication Pages">
                                <iconify-icon icon="solar:lock-keyhole-line-duotone" class="fs-7"></iconify-icon>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="sidebarmenu">
                <div class="brand-logo d-flex align-items-center nav-logo">
                    <a href="<?= Url::to(['/dashboard/index']) ?>" class="text-nowrap logo-img">
                        <img src="<?= $setting && $setting->logo_bottom ? Html::encode($setting->logo_bottom) : '/template/assets/images/logos/logo.svg' ?>" alt="Logo" />
                    </a>
                </div>

                <nav class="sidebar-nav <?= ($activeMiniId === 'mini-1') ? 'active' : '' ?>" id="menu-right-mini-1">
                    <ul class="sidebar-menu" id="sidebarnav">
                        <!-- Dashboard qismi -->
                        <li class="nav-small-cap"><span class="hide-menu">Dashboards</span></li>

                        <li class="sidebar-item <?= isActiveDashboardItem(['index']) ?>">
                            <a class="sidebar-link" href="<?= Url::to(['/dashboard/index']) ?>">
                                <iconify-icon icon="solar:chart-line-duotone"></iconify-icon>
                                Dashboard
                            </a>
                        </li>

                        <?php if ($this->context->hasRole('creator')): ?>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?= Url::to(['/dashboard-creator/index']) ?>">
                                    <iconify-icon icon="solar:atom-line-duotone"></iconify-icon>
                                    Creator Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($this->context->hasRole('admin')): ?>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?= Url::to(['/dashboard-admin/index']) ?>">
                                    <iconify-icon icon="solar:screencast-2-line-duotone"></iconify-icon>
                                    Admin Dashboard
                                </a>
                            </li>
                        <?php endif; ?>





                    </ul>
                </nav>

                <nav class="sidebar-nav <?= ($activeMiniId === 'mini-7') ? 'active' : '' ?>" id="menu-right-mini-7">
                    <ul class="sidebar-menu" id="sidebarnav">
<!--                        --><?php //if ($this->context->hasRole('creator') || $this->context->hasRole('admin')): ?>
<!--                            <li class="sidebar-item">-->
<!--                                <a class="sidebar-link has-arrow" href="javascript:void(0)">-->
<!--                                    <iconify-icon icon="solar:home-angle-line-duotone"></iconify-icon>-->
<!--                                    Front Pages-->
<!--                                </a>-->
<!--                                <ul class="collapse first-level">-->
<!--                                    <li class="sidebar-item">-->
<!--                                        <a class="sidebar-link" href="../main/frontend-landingpage.html">-->
<!--                                            Homepage-->
<!--                                        </a>-->
<!--                                    </li>-->
<!--                                    <li class="sidebar-item">-->
<!--                                        <a class="sidebar-link" href="../main/frontend-aboutpage.html">-->
<!--                                            About Us-->
<!--                                        </a>-->
<!--                                    </li>-->
<!--                                </ul>-->
<!--                            </li>-->
<!--                        --><?php //endif; ?>

                        <li><span class="sidebar-divider"></span></li>

                        <?php foreach ($categories as $category): ?>
                            <li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                    <iconify-icon icon="solar:align-left-line-duotone"></iconify-icon>
                                    <span class="hide-menu"><?= Html::encode($category->title) ?></span>
                                </a>
                                <ul aria-expanded="false" class="collapse first-level">
                                    <?php foreach ($category->courses as $course): ?>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                                <span class="icon-small"></span>
                                                <span class="hide-menu text-wrap d-block" style="white-space: normal; word-break: break-word;">
                                                    <?= Html::encode($course->title) ?>
                                                </span>
                                            </a>
                                            <ul aria-expanded="false" class="collapse two-level">
                                                <?php foreach ($course->lessons as $lesson): ?>
                                                    <li class="sidebar-item">
                                                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                                            <span class="icon-small"></span>
                                                            <span class="hide-menu text-wrap d-block" style="white-space: normal; word-break: break-word;">
                                                                <?= Html::encode($lesson->title) ?>
                                                            </span>
                                                        </a>
                                                        <ul aria-expanded="false" class="collapse three-level">
                                                            <?php foreach ($lesson->parts as $part): ?>
                                                                <li class="sidebar-item">
                                                                    <a href="<?= Url::to(['dashboard/part-content-index', 'part_id' => $part->id]) ?>" class="sidebar-link">
                                                                        <span class="icon-small"></span>
                                                                        <span class="hide-menu text-wrap d-block" style="white-space: normal; word-break: break-word;">
                                                                            <?= Html::encode($part->title) ?>
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>


                        <li><span class="sidebar-divider"></span></li>
                        <!-- Apps qismi -->
                        <li class="nav-small-cap"><span class="hide-menu">All in one</span></li>

                        <li class="sidebar-item <?= $isCategory || $isCourse || $isLesson || $isPart ? 'active' : '' ?>">
                            <a class="sidebar-link has-arrow <?= $isCategory || $isCourse || $isLesson || $isPart ? '' : 'collapsed' ?>"
                               href="javascript:void(0)"
                                <?= $isCategory || $isCourse || $isLesson || $isPart ? 'aria-expanded="true"' : 'aria-expanded="false"' ?>>
                                <iconify-icon icon="solar:cart-3-line-duotone"></iconify-icon>
                                All Categories
                            </a>
                            <ul class="collapse first-level <?= $isCategory || $isCourse || $isLesson || $isPart ? 'show' : '' ?>">
                                <li class="sidebar-item <?= $isCategory ? 'active' : '' ?>">
                                    <a class="sidebar-link" href="<?= Url::to(['/dashboard/category-index']) ?>">Categories</a>
                                </li>
                                <li class="sidebar-item <?= $isCourse ? 'active' : '' ?>">
                                    <a class="sidebar-link" href="<?= Url::to(['/dashboard/course-all']) ?>">Courses</a>
                                </li>
                                <li class="sidebar-item <?= $isLesson ? 'active' : '' ?>">
                                    <a class="sidebar-link" href="<?= Url::to(['/dashboard/lesson-all']) ?>">Lessons</a>
                                </li>
                                <li class="sidebar-item <?= $isPart ? 'active' : '' ?>">
                                    <a class="sidebar-link" href="<?= Url::to(['/dashboard/part-all']) ?>">Parts</a>
                                </li>
                            </ul>
                        </li>


                        <li class="nav-small-cap"><span class="hide-menu">Setting</span></li>

                        <li class="sidebar-item <?= isActiveDashboardItem(['admin-setting']) ?>">
                            <a class="sidebar-link" href="<?= Url::to(['/dashboard/admin-setting']) ?>">
                                <iconify-icon icon="solar:feed-line-duotone"></iconify-icon>
                                Setting
                            </a>
                        </li>

                        <li class="nav-small-cap"><span class="hide-menu">Roles</span></li>

                        <li class="sidebar-item <?= isActiveDashboardItem(['admin-user']) ?>">
                            <a class="sidebar-link" href="<?= Url::to(['/dashboard/admin-user']) ?>">
                                <iconify-icon icon="solar:shield-user-line-duotone"></iconify-icon>
                                Users
                            </a>
                        </li>

                        <li class="sidebar-item <?= isActiveDashboardItem(['admin-roles']) ?>">
                            <a class="sidebar-link" href="<?= Url::to(['/dashboard/admin-roles']) ?>">
                                <iconify-icon icon="solar:bug-minimalistic-line-duotone"></iconify-icon>
                                Roles
                            </a>
                        </li>

                    </ul>
                </nav>

                <nav class="sidebar-nav <?= ($activeMiniId === 'mini-9') ? 'active' : '' ?>" id="menu-right-mini-9">
                    <ul class="sidebar-menu" id="sidebarnav">
                        <!-- Auth qismi -->
                        <li class="nav-small-cap"><span class="hide-menu">Auth</span></li>

                        <li class="sidebar-item <?= isActiveDashboardItem(['profile']) ?>">
                            <a class="sidebar-link" href="<?= Url::to(['/dashboard/profile']) ?>">
                                <iconify-icon icon="solar:shield-user-line-duotone"></iconify-icon>
                                User Profile
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="<?= Url::to(['/auth/logout']) ?>">
                                <iconify-icon icon="solar:login-3-line-duotone"></iconify-icon>
                                Logout
                            </a>
                        </li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</aside>
