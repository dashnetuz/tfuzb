<?php

namespace frontend\helpers;

use Yii;

class SidebarHelper
{
    public static function resolveSectionId(): string
    {
        $id = Yii::$app->controller->id ?? '';

        // Bularning barchasi mini-7 ga tegishli bo‘lishi kerak
        $mini7 = ['category', 'course', 'lesson', 'part', 'admin-user', 'admin-roles', 'admin-setting'];
        $mini9 = ['profile'];

        if (in_array($id, $mini7)) {
            return 'mini-7';
        }
        if (in_array($id, $mini9)) {
            return 'mini-9';
        }

        return 'mini-1';
    }

    public static function isGroupActive(string $group): bool
    {
        $id = Yii::$app->controller->id ?? '';
        return $id === $group;
    }

    public static function isGroupSection(string $group): bool
    {
        $id = Yii::$app->controller->id ?? '';
        return strpos($id, $group) === 0;
    }


    public static function getSidebarType(): string
    {
        return self::resolveSectionId() ? 'full' : 'mini-sidebar';
    }
}
