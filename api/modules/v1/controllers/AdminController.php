<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\rest\Controller;
use OpenApi\Annotations as OA;
use common\models\User;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin-only endpointlar"
 * )
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * JWT token orqali rollarni aniqlash
     */
    protected function getRolesFromToken(): array
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        if (!$authHeader || !preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            return [];
        }

        try {
            $decoded = \Firebase\JWT\JWT::decode($matches[1], new \Firebase\JWT\Key(Yii::$app->params['jwtSecret'], 'HS256'));
            return $decoded->roles ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/admin/users",
     *     summary="Foydalanuvchilar ro'yxati (faqat creator va admin)",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Foydalanuvchilar ro'yxati",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="username", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="status", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Ruxsat yo‘q")
     * )
     */
    public function actionUsers()
    {
        $roles = $this->getRolesFromToken();

        if (!in_array('creator', $roles) && !in_array('admin', $roles)) {
            throw new ForbiddenHttpException("Sizda bu endpointga ruxsat yo‘q.");
        }

        return User::find()
            ->select(['id', 'username', 'email', 'status'])
            ->asArray()
            ->all();
    }
}
