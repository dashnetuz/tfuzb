<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use OpenApi\Annotations as OA;
use common\models\Permission;
use common\models\Role;
use common\models\RolePermission;

/**
 * @OA\Tag(
 *     name="Permission",
 *     description="Permissionlar bilan ishlash"
 * )
 */
class PermissionController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/v1/permission/list",
     *     summary="Barcha permissionlar ro‘yxati",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Permissionlar",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string")
     *         ))
     *     )
     * )
     */
    public function actionList()
    {
        $this->requirePermission('can_view_permissions');
        return Permission::find()->select(['id', 'name'])->asArray()->all();
    }

    /**
     * @OA\Get(
     *     path="/v1/permission/by-role",
     *     summary="Rolga biriktirilgan permissionlar",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Permissionlar", @OA\JsonContent(type="array", @OA\Items(type="string")))
     * )
     */
    public function actionByRole()
    {
        $this->requirePermission('can_view_permissions_by_role');

        $roleName = Yii::$app->request->get('role');
        $role = Role::findOne(['name' => $roleName]);

        if (!$role) {
            throw new NotFoundHttpException("Rol topilmadi.");
        }

        return array_column(
            RolePermission::find()
                ->alias('rp')
                ->select('p.name')
                ->leftJoin('permission p', 'p.id = rp.permission_id')
                ->where(['rp.role_id' => $role->id])
                ->asArray()
                ->all(),
            'name'
        );
    }

    /**
     * @OA\Post(
     *     path="/v1/permission/create",
     *     summary="Yangi permission yaratish",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="can_create_post")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permission yaratildi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionCreate()
    {
        $this->requirePermission('can_create_permission');

        $name = Yii::$app->request->post('name', '');

        if (!$name || !preg_match('/^[a-z0-9_]{3,64}$/', $name)) {
            throw new BadRequestHttpException("Noto‘g‘ri permission nomi.");
        }

        if (Permission::findOne(['name' => $name])) {
            throw new BadRequestHttpException("Bu permission allaqachon mavjud.");
        }

        $permission = new Permission();
        $permission->name = $name;
        $permission->created_at = time(); // Qo‘shildi
        $permission->updated_at = time(); // Qo‘shildi

        if ($permission->save()) {
            return ['message' => "Permission '{$name}' yaratildi."];
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $permission->errors];
    }

    /**
     * @OA\Delete(
     *     path="/v1/permission/delete",
     *     summary="Permission o‘chirish",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="can_create_post")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permission o‘chirildi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionDelete()
    {
        $this->requirePermission('can_delete_permission');

        $name = Yii::$app->request->post('name');

        $permission = Permission::findOne(['name' => $name]);
        if (!$permission) {
            throw new NotFoundHttpException("Permission topilmadi.");
        }

        RolePermission::deleteAll(['permission_id' => $permission->id]);
        $permission->delete();

        return ['message' => "Permission '{$name}' o‘chirildi."];
    }

    /**
     * @OA\Post(
     *     path="/v1/permission/assign-to-role",
     *     summary="Permissionni rolga biriktirish",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role", "permission"},
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="permission", type="string", example="can_create_post")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permission biriktirildi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionAssignToRole()
    {
        $this->requirePermission('can_assign_permission');

        $roleName = Yii::$app->request->post('role');
        $permName = Yii::$app->request->post('permission');

        if (!$roleName || !$permName) {
            throw new BadRequestHttpException("Rol va permission qiymatlari kerak.");
        }

        $role = Role::findOne(['name' => $roleName]);
        $permission = Permission::findOne(['name' => $permName]);

        if (!$role || !$permission) {
            throw new NotFoundHttpException("Rol yoki permission topilmadi.");
        }

        $exists = RolePermission::find()
            ->where(['role_id' => $role->id, 'permission_id' => $permission->id])
            ->exists();

        if ($exists) {
            return ['message' => "Bu permission allaqachon rolga biriktirilgan."];
        }

        $rp = new RolePermission([
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        if (!$rp->save()) {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $rp->errors];
        }

        return ['message' => "Permission '{$permission->name}' roldan '{$role->name}'ga biriktirildi."];
    }

    /**
     * @OA\Delete(
     *     path="/v1/permission/remove-from-role",
     *     summary="Permissionni roldan olib tashlash",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role", "permission"},
     *             @OA\Property(property="role", type="string"),
     *             @OA\Property(property="permission", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permission olib tashlandi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionRemoveFromRole()
    {
        $this->requirePermission('can_remove_permission');

        $roleName = Yii::$app->request->post('role');
        $permName = Yii::$app->request->post('permission');

        if (!$roleName || !$permName) {
            throw new BadRequestHttpException("Rol va permission qiymatlari kerak.");
        }

        $role = Role::findOne(['name' => $roleName]);
        $permission = Permission::findOne(['name' => $permName]);

        if (!$role || !$permission) {
            throw new NotFoundHttpException("Rol yoki permission topilmadi.");
        }

        RolePermission::deleteAll([
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        return ['message' => "Permission '{$permission->name}' roldan '{$role->name}'dan olib tashlandi."];
    }


    /**
     * @OA\Put(
     *     path="/v1/permission/update-role-permissions",
     *     summary="Roledagi permissionlar ro‘yxatini to‘liq yangilash",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role", "permissions"},
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"can_view", "can_create", "can_edit"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissionlar yangilandi",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="added", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="skipped", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function actionUpdateRolePermissions()
    {
        $this->requirePermission('can_assign_permission');

        $roleName = Yii::$app->request->bodyParams['role'] ?? null;
        $permissions = Yii::$app->request->bodyParams['permissions'] ?? null;

        if (!$roleName || !is_array($permissions)) {
            throw new BadRequestHttpException("Rol nomi va permissionlar massiv ko‘rinishida bo‘lishi kerak.");
        }

        $role = Role::findOne(['name' => $roleName]);
        if (!$role) {
            throw new NotFoundHttpException("Rol topilmadi.");
        }

        // Eski permissionlarni o‘chirish
        RolePermission::deleteAll(['role_id' => $role->id]);

        $added = [];
        $skipped = [];

        foreach ($permissions as $permName) {
            $permission = Permission::findOne(['name' => $permName]);

            if (!$permission) {
                $skipped[] = $permName;
                continue;
            }

            $rp = new RolePermission([
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);

            if ($rp->save()) {
                $added[] = $permName;
            } else {
                $skipped[] = $permName;
            }
        }

        return [
            'message' => "Permissionlar yangilandi.",
            'added' => $added,
            'skipped' => $skipped,
        ];
    }


    /**
     * @OA\Post(
     *     path="/v1/permission/assign-multiple-to-role",
     *     summary="Roledagi mavjud permissionlarga qo‘shimcha bir nechta permission biriktirish",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role", "permissions"},
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"can_export", "can_import"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permissionlar biriktirildi", @OA\JsonContent(
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(property="added", type="array", @OA\Items(type="string")),
     *         @OA\Property(property="skipped", type="array", @OA\Items(type="string"))
     *     ))
     * )
     */
    public function actionAssignMultipleToRole()
    {
        $this->requirePermission('can_assign_permission');

        $roleName = Yii::$app->request->post('role');
        $permissions = Yii::$app->request->post('permissions');

        if (!$roleName || !is_array($permissions) || empty($permissions)) {
            throw new BadRequestHttpException("Rol nomi va permissionlar ro‘yxati kerak.");
        }

        $role = Role::findOne(['name' => $roleName]);
        if (!$role) {
            throw new NotFoundHttpException("Rol topilmadi.");
        }

        $added = [];
        $skipped = [];

        foreach ($permissions as $permName) {
            $permission = Permission::findOne(['name' => $permName]);
            if (!$permission) {
                $skipped[] = $permName;
                continue;
            }

            $exists = RolePermission::find()
                ->where(['role_id' => $role->id, 'permission_id' => $permission->id])
                ->exists();

            if ($exists) {
                $skipped[] = $permName;
                continue;
            }

            $rp = new RolePermission([
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
            if ($rp->save()) {
                $added[] = $permName;
            } else {
                $skipped[] = $permName;
            }
        }

        return [
            'message' => 'Permissionlar biriktirildi.',
            'added' => $added,
            'skipped' => $skipped,
        ];
    }

    /**
     * @OA\Post(
     *     path="/v1/permission/remove-multiple-from-role",
     *     summary="Roledan bir nechta permissionni olib tashlash",
     *     tags={"Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role", "permissions"},
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"can_view", "can_delete"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permissionlar olib tashlandi", @OA\JsonContent(
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(property="removed", type="array", @OA\Items(type="string")),
     *         @OA\Property(property="skipped", type="array", @OA\Items(type="string"))
     *     ))
     * )
     */
    public function actionRemoveMultipleFromRole()
    {
        $this->requirePermission('can_remove_permission');

        $roleName = Yii::$app->request->post('role');
        $permissions = Yii::$app->request->post('permissions');

        if (!$roleName || !is_array($permissions) || empty($permissions)) {
            throw new BadRequestHttpException("Rol nomi va permissionlar ro‘yxati kerak.");
        }

        $role = Role::findOne(['name' => $roleName]);
        if (!$role) {
            throw new NotFoundHttpException("Rol topilmadi.");
        }

        $removed = [];
        $skipped = [];

        foreach ($permissions as $permName) {
            $permission = Permission::findOne(['name' => $permName]);
            if (!$permission) {
                $skipped[] = $permName;
                continue;
            }

            $deleted = RolePermission::deleteAll([
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);

            if ($deleted) {
                $removed[] = $permName;
            } else {
                $skipped[] = $permName;
            }
        }

        return [
            'message' => 'Permissionlar roldan olib tashlandi.',
            'removed' => $removed,
            'skipped' => $skipped,
        ];
    }

}
