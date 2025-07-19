<?php

namespace frontend\helpers;

use Yii;
use frontend\helpers\ApiClient;
use Throwable;

class PermissionHelper
{
    public static function assignCoursePermissions(string $prefix, int $id, array $user): void
    {
        $role = "{$prefix}_id_{$id}";
        $permissions = [
            "{$prefix}_view_id_{$id}",
            "{$prefix}_update_id_{$id}",
            "{$prefix}_delete_id_{$id}",
        ];

        // 1. Role yaratish
        try {
            ApiClient::request('POST', '/v1/role/create', ['name' => $role]);
        } catch (Throwable $e) {
            Yii::warning("⚠️ Role yaratilmadi yoki mavjud: {$role}. Xabar: " . $e->getMessage(), 'PermissionHelper');
        }

        // 2. Permissionlar yaratish
        foreach ($permissions as $perm) {
            try {
                ApiClient::request('POST', '/v1/permission/create', ['name' => $perm]);
            } catch (Throwable $e) {
                Yii::warning("⚠️ Permission mavjud yoki xato: {$perm}. Xabar: " . $e->getMessage(), 'PermissionHelper');
            }
        }

        // 3. Rolega permissionlarni biriktirish
        try {
            ApiClient::request('POST', '/v1/permission/assign-multiple-to-role', [
                'role' => $role,
                'permissions' => $permissions,
            ]);
        } catch (Throwable $e) {
            Yii::error("❌ Permissionlar rolega biriktirib bo‘lmadi: {$role}", 'PermissionHelper');
        }

        // 4. Role foydalanuvchiga biriktirish
        try {
            ApiClient::request('POST', '/v1/role/assign', [
                'user_id' => $user['id'],
                'role' => $role,
            ]);
        } catch (Throwable $e) {
            Yii::error("❌ Foydalanuvchiga rol biriktirib bo‘lmadi: user_id={$user['id']}, role={$role}", 'PermissionHelper');
        }

        // 5. Admin va creator foydalanuvchilarga biriktirish
        try {
            $allUsers = ApiClient::request('GET', '/v1/role/list');
        } catch (Throwable $e) {
            Yii::error("❌ Barcha foydalanuvchilar ro‘yxatini olishda xatolik: " . $e->getMessage(), 'PermissionHelper');
            return;
        }

        foreach ($allUsers as $usr) {
            if (!isset($usr['id'], $usr['roles']) || $usr['id'] == $user['id']) {
                continue;
            }

            foreach (['admin', 'creator'] as $superRole) {
                if (in_array($superRole, $usr['roles'])) {
                    try {
                        ApiClient::request('POST', '/v1/role/assign', [
                            'user_id' => $usr['id'],
                            'role' => $role,
                        ]);
                    } catch (Throwable $e) {
                        Yii::warning("⚠️ Rolni admin/creator foydalanuvchiga biriktirishda muammo: user_id={$usr['id']}, role={$role}", 'PermissionHelper');
                    }
                    break;
                }
            }
        }
    }
}
