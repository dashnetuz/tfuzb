<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use OpenApi\Annotations as OA;
use common\models\User;
use common\models\UserRole;
use common\models\Role;

/**
 * @OA\Tag(
 *     name="Role",
 *     description="Rollarni boshqarish (Creator va Admin)"
 * )
 */
class RoleController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/v1/role/all",
     *     summary="Barcha mavjud rollar ro'yxati",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Rollar ro'yxati",
     *         @OA\JsonContent(type="array", @OA\Items(type="string"))
     *     )
     * )
     */
    public function actionAll()
    {
        $this->requirePermission('can_view_roles');
        return Role::find()->select('name')->orderBy('name')->column();
    }

    /**
     * @OA\Get(
     *     path="/v1/role/list",
     *     summary="Barcha foydalanuvchilar va ularning rollari",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Foydalanuvchilar roli bilan",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"))
     *         ))
     *     )
     * )
     */
    public function actionList()
    {
        $this->requirePermission('can_view_users_with_roles');

        $users = User::find()->with(['roles.role'])->all();

        return array_map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'status' => $user->status,
                'roles' => array_map(fn($ur) => $ur->role->name, $user->roles),
            ];
        }, $users);
    }

    /**
     * @OA\Post(
     *     path="/v1/role/create",
     *     summary="Yangi rol yaratish",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol yaratildi",
     *         @OA\JsonContent(@OA\Property(property="message", type="string"))
     *     )
     * )
     */
    public function actionCreate()
    {
        $this->requirePermission('can_create_role');

        $name = Yii::$app->request->bodyParams['name'] ?? null;
        if (!$name || !preg_match('/^[a-z0-9_]{3,64}$/', $name)) {
            throw new BadRequestHttpException("Rol nomi noto‘g‘ri yoki bo‘sh");
        }

        if (Role::find()->where(['name' => $name])->exists()) {
            throw new BadRequestHttpException("Bu nomdagi rol allaqachon mavjud");
        }

        $model = new Role();
        $model->name = $name;
        $model->created_at = time();
        $model->updated_at = time();

        if ($model->save()) {
            return ['message' => "Rol '{$name}' yaratildi."];
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $model->getErrors()];
    }

    /**
     * @OA\Delete(
     *     path="/v1/role/delete",
     *     summary="Rolni o‘chirish",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rol o‘chirildi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionDelete()
    {
        $this->requirePermission('can_delete_role');

        $name = Yii::$app->request->bodyParams['name'] ?? null;
        $role = Role::findOne(['name' => $name]);

        if (!$role) {
            throw new NotFoundHttpException("Bunday rol topilmadi.");
        }

        if (UserRole::find()->where(['role_id' => $role->id])->exists()) {
            throw new ForbiddenHttpException("Avval bu rol foydalanuvchilardan olib tashlanishi kerak.");
        }

        $role->delete();

        return ['message' => "Rol '{$name}' o‘chirildi."];
    }

    /**
     * @OA\Post(
     *     path="/v1/role/assign",
     *     summary="Foydalanuvchiga rol tayinlash",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "role"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rol tayinlandi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionAssign()
    {
        $this->requirePermission('can_assign_roles');

        $data = Yii::$app->request->bodyParams;
        $userId = $data['user_id'] ?? null;
        $roleName = $data['role'] ?? null;

        $user = User::findOne($userId);
        $role = Role::findOne(['name' => $roleName]);

        if (!$user || !$role) {
            throw new NotFoundHttpException("Foydalanuvchi yoki rol topilmadi.");
        }

        $exists = UserRole::find()->where(['user_id' => $userId, 'role_id' => $role->id])->exists();
        if ($exists) {
            return ['message' => "Bu foydalanuvchida '{$roleName}' roli allaqachon mavjud."];
        }

        $ur = new UserRole();
        $ur->user_id = $userId;
        $ur->role_id = $role->id;
        $ur->created_at = time();
        $ur->updated_at = time();
        $ur->save(false);

        return ['message' => "Rol '{$roleName}' foydalanuvchiga biriktirildi."];
    }

    /**
     * @OA\Post(
     *     path="/v1/role/assign-multiple",
     *     summary="Foydalanuvchiga bir nechta rollarni biriktirish",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "roles"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rollar biriktirildi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionAssignMultiple()
    {
        $this->requirePermission('can_assign_roles');

        $body = Yii::$app->request->bodyParams;
        $userId = $body['user_id'] ?? null;
        $roleNames = $body['roles'] ?? [];

        if (!$userId || !is_array($roleNames)) {
            throw new BadRequestHttpException("Foydalanuvchi ID va rollar talab qilinadi.");
        }

        $user = User::findOne($userId);
        if (!$user) {
            throw new NotFoundHttpException("Foydalanuvchi topilmadi.");
        }

        $assigned = [];
        foreach ($roleNames as $roleName) {
            $role = Role::findOne(['name' => $roleName]);
            if (!$role) continue;

            $exists = UserRole::find()->where([
                'user_id' => $user->id,
                'role_id' => $role->id
            ])->exists();

            if (!$exists) {
                $userRole = new UserRole();
                $userRole->user_id = $user->id;
                $userRole->role_id = $role->id;
                $userRole->created_at = time();
                $userRole->updated_at = time();
                $userRole->save(false);
                $assigned[] = $roleName;
            }
        }

        return ['message' => "Quyidagi rollar biriktirildi: " . implode(', ', $assigned)];
    }

    /**
     * @OA\Delete(
     *     path="/v1/role/remove",
     *     summary="Foydalanuvchidan rolni olib tashlash",
     *     tags={"Role"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "role"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rol olib tashlandi", @OA\JsonContent(@OA\Property(property="message", type="string")))
     * )
     */
    public function actionRemove()
    {
        $this->requirePermission('can_remove_role');

        $body = Yii::$app->request->bodyParams;
        $userId = $body['user_id'] ?? null;
        $roleName = $body['role'] ?? null;

        $role = Role::findOne(['name' => $roleName]);
        if (!$role) {
            throw new NotFoundHttpException("Rol topilmadi.");
        }

        $userRole = UserRole::findOne(['user_id' => $userId, 'role_id' => $role->id]);
        if (!$userRole) {
            throw new NotFoundHttpException("Foydalanuvchida bunday rol mavjud emas.");
        }

        $userRole->delete();

        return ['message' => "Rol '{$roleName}' olib tashlandi."];
    }
}
