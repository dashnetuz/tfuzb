<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use OpenApi\Annotations as OA;
use common\models\User;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin statistikasi (faqat creator & admin)"
 * )
 */
class AdminStatsController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/v1/admin/stats",
     *     summary="Admin panel statistikasi",
     *     tags={"Admin"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistik ma'lumotlar",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_users", type="integer"),
     *             @OA\Property(property="active_users", type="integer"),
     *             @OA\Property(
     *                 property="role_counts",
     *                 type="object",
     *                 additionalProperties={"type":"integer"}
     *             ),
     *             @OA\Property(
     *                 property="latest_users",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="username", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Ruxsat yo'q")
     * )
     */
    public function actionStats()
    {
        $roles = $this->getRolesFromToken();

        if (!in_array('creator', $roles) && !in_array('admin', $roles)) {
            throw new ForbiddenHttpException("Faqat Creator yoki Admin statistikani ko'rishi mumkin.");
        }

        $totalUsers = User::find()->count();
        $activeUsers = User::find()->where(['status' => User::STATUS_ACTIVE])->count();

        $roleCountsRaw = (new \yii\db\Query())
            ->select(['r.name as role', 'COUNT(*) as count'])
            ->from('user_role ur')
            ->innerJoin('role r', 'ur.role_id = r.id')
            ->groupBy('r.name')
            ->all();

        $roleCounts = [];
        foreach ($roleCountsRaw as $row) {
            $roleCounts[$row['role']] = (int)$row['count'];
        }

        $latestUsers = User::find()
            ->select(['id', 'username', 'email', 'created_at'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        return [
            'total_users' => (int)$totalUsers,
            'active_users' => (int)$activeUsers,
            'role_counts' => $roleCounts,
            'latest_users' => $latestUsers,
        ];
    }
}
