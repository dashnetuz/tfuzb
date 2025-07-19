<?php

namespace frontend\controllers;

use common\models\ContentPdf;
use common\models\ContentPicture;
use common\models\ContentText;
use common\models\ContentType;
use common\models\ContentVideo;
use common\models\PartContent;
use common\models\Quiz;
use common\models\QuizOption;
use common\models\QuizQuestion;
use common\models\EssayCriteria;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\helpers\ApiClient;
use frontend\helpers\PermissionHelper;
use frontend\helpers\PositionHelper;
use frontend\models\AccountSettingsForm;
use frontend\models\ResetPasswordForm;

use common\models\Setting;
use common\models\Category;
use common\models\Course;
use common\models\Lesson;
use common\models\Part;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

class DashboardAdminController extends Controller
{
    public $layout = 'dashboard';
    public $user = null;
    public $token = null;

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->token = Yii::$app->session->get('user_token');
        $profile = Yii::$app->session->get('user_profile');

        if (!$this->token || !$profile || !is_array($profile)) {
            $this->flashError(Yii::t('app', 'Sessiya tugagan. Qayta login qiling.'));
            return $this->redirect(['auth/login']);
        }

        $this->user = $profile;

        // ✅ Har doim mavjud bo‘lishi uchun layout fayllarga yuboramiz
        Yii::$app->view->params['user'] = $profile;

        return true;
    }


    public function hasRole($role)
    {
        if (!$this->user || empty($this->user['roles'])) {
            return false;
        }
        return in_array($role, $this->user['roles']);
    }

    public function hasPermission($permission)
    {
        if (!$this->user || empty($this->user['permissions'])) {
            return false;
        }
        return in_array($permission, $this->user['permissions']);
    }

    public function actionIndex()
    {
        if (!$this->hasPermission('can_view_profile_me') && !$this->hasRole('user')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        return $this->render('index', [
            'user' => $this->user,
        ]);
    }

    public function actionProfile()
    {
        if (!$this->hasRole('user')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        $profile = ApiClient::request('GET', '/v1/user/profile');
        if (!$profile) {
            return $this->redirect(['dashboard/index']);
        }

        $user = new AccountSettingsForm();
        $user->id = $profile['id'] ?? null;
        $user->username = $profile['username'] ?? null;
        $user->email = $profile['email'] ?? null;
        $user->firstname = $profile['firstname'] ?? '';
        $user->lastname = $profile['lastname'] ?? '';
        $user->avatar = $profile['avatar'] ?? null;
        $user->roles = $profile['roles'] ?? [];

        $resetPasswordForm = new ResetPasswordForm(); // ✅ Parol formasi ham

        return $this->render('profile', [
            'user' => $user,
            'resetPasswordForm' => $resetPasswordForm,
        ]);
    }
    public function actionResetPassword()
    {
        if (!$this->hasRole('user')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        $model = new ResetPasswordForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $payload = [
                'current_password' => $model->current_password,
                'new_password' => $model->new_password,
            ];

            $result = ApiClient::request('POST', '/v1/user/reset-password', $payload);

            if (isset($result['message'])) {
                $this->flashSuccess(Yii::t('app', $result['message']));
            } elseif (isset($result['error'])) {
                $this->flashError($result['error']);
            } else {
                $this->flashError(Yii::t('app', 'Noto\'g\'ri parol terildi!'));
            }

            return $this->redirect(['dashboard/profile']);
        }

        $this->flashError(Yii::t('app', 'Maʼlumotlarni toʻgʻri toʻldiring.'));
        return $this->redirect(['dashboard/profile']);
    }
    public function actionProfileUpdate()
    {
        if (!$this->hasRole('user')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $payload = [
                'firstname' => $post['AccountSettingsForm']['firstname'] ?? '',
                'lastname' => $post['AccountSettingsForm']['lastname'] ?? '',
            ];

            $result = ApiClient::request('PUT', '/v1/user/profile/update', $payload);

            if (isset($result['message'])) {
                Yii::$app->session->setFlash('success', Yii::t('app', $result['message']));
            } elseif (isset($result['errors'])) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Yangilashda xatolik yuz berdi'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Nomaʼlum xatolik yuz berdi'));
            }

            return $this->redirect(['dashboard/profile']);
        }

        throw new \yii\web\BadRequestHttpException('Noto‘g‘ri so‘rov.');
    }
    public function actionAvatarUpload()
    {
        if (!$this->hasRole('user')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName('avatar');

            if ($file) {
                $postData = [
                    'avatar' => [
                        'name' => $file->name,
                        'tempName' => $file->tempName,
                        'type' => $file->type,
                    ],
                ];

                $result = ApiClient::request('POST', '/v1/user/avatar-upload', $postData, true);

                if (isset($result['url'])) {
                    $user = ApiClient::request('GET', '/v1/user/profile');
                    if ($user) {
                        Yii::$app->session->set('user_profile', $user);
                        $this->user = $user;
                    }
                    $this->flashSuccess(Yii::t('app', 'Avatar muvaffaqiyatli yuklandi.'));
                } else {
                    $this->flashError($result['error'] ?? Yii::t('app', 'Avatar yuklashda xatolik.'));
                }
            } else {
                $this->flashError(Yii::t('app', 'Fayl tanlanmadi.'));
            }
        }
        $this->flashSuccess(Yii::t('app', 'Avatar muvaffaqiyatli yuklandi.'));
        return $this->redirect(['dashboard/profile']);
    }



    public function actionForbidden()
    {
        return $this->render('forbidden');
    }
    protected function flashSuccess($message)
    {
        Yii::$app->session->setFlash('success', $message);
    }
    protected function flashError($message)
    {
        Yii::$app->session->setFlash('error', $message);
    }
    protected function flashWarning($message)
    {
        Yii::$app->session->setFlash('warning', $message);
    }
    protected function flashInfo($message)
    {
        Yii::$app->session->setFlash('info', $message);
    }


//ADMINLAR ISHLASHI UCHUN
    public function actionAdminUser()
    {
        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        $users = ApiClient::request('GET', '/v1/role/list');

        return $this->render('admin/user/index', [
            'users' => $users,
        ]);
    }

    public function actionAdminUserManageRoles($id)
    {
        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        $allRoles = ApiClient::request('GET', '/v1/role/all');
        $users = ApiClient::request('GET', '/v1/role/list');

        $user = null;
        foreach ($users as $u) {
            if ($u['id'] == $id) {
                $user = $u;
                break;
            }
        }

        if (!$user) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Foydalanuvchi topilmadi.'));
            return $this->redirect(['dashboard/admin-user']);
        }

        $assignedRoles = $user['roles'] ?? [];

        // 🔥 Agar admin bo‘lsa, "creator" rolini ko‘rsatmaymiz
        if ($this->hasRole('admin') && !$this->hasRole('creator')) {
            $allRoles = array_filter($allRoles, function($role) {
                return $role !== 'creator';
            });
        }

        $availableRoles = array_diff($allRoles, $assignedRoles);

        return $this->render('admin/user/manage-roles', [
            'userId' => $id,
            'username' => $user['username'],
            'email' => $user['email'],
            'assignedRoles' => $assignedRoles,
            'availableRoles' => $availableRoles,
        ]);
    }

    public function actionAssignRoleApi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return ['error' => Yii::t('app', 'Sizga bu amalni bajarishga ruxsat yo‘q.')];
        }

        $userId = Yii::$app->request->post('user_id');
        $role = Yii::$app->request->post('role');

        // 🔥 Agar admin bo‘lsa, "creator" rolini biriktira olmaydi
        if ($this->hasRole('admin') && !$this->hasRole('creator') && $role === 'creator') {
            return ['error' => Yii::t('app', 'Siz creator rolini biriktira olmaysiz.')];
        }

        $result = ApiClient::request('POST', '/v1/role/assign', [
            'user_id' => $userId,
            'role' => $role,
        ]);

        if (isset($result['message'])) {
            return ['success' => $result['message']];
        } elseif (isset($result['error'])) {
            return ['error' => $result['error']];
        } else {
            return ['error' => Yii::t('app', 'Nomaʼlum xatolik yuz berdi.')];
        }
    }

    public function actionRemoveRoleApi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return ['error' => Yii::t('app', 'Sizga bu amalni bajarishga ruxsat yo‘q.')];
        }

        $userId = Yii::$app->request->post('user_id');
        $role = Yii::$app->request->post('role');

        // 🔥 Agar admin bo‘lsa, "creator" rolini olib tashlay olmaydi
        if ($this->hasRole('admin') && !$this->hasRole('creator') && $role === 'creator') {
            return ['error' => Yii::t('app', 'Siz creator rolini olib tashlay olmaysiz.')];
        }

        $result = ApiClient::request('DELETE', '/v1/role/remove', [
            'user_id' => $userId,
            'role' => $role,
        ]);

        if (isset($result['message'])) {
            return ['success' => $result['message']];
        } elseif (isset($result['error'])) {
            return ['error' => $result['error']];
        } else {
            return ['error' => Yii::t('app', 'Nomaʼlum xatolik yuz berdi.')];
        }
    }


    public function actionAdminRoles()
    {
        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        $roles = ApiClient::request('GET', '/v1/role/all');

        return $this->render('admin/role/index', [
            'roles' => is_array($roles) ? $roles : [],
        ]);
    }

    public function actionManageRolePermissions($role)
    {
        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        return $this->render('admin/role/manage-permissions', [
            'role' => $role,
        ]);
    }

    public function actionAllPermissions()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $permissions = ApiClient::request('GET', '/v1/permission/list');

        return is_array($permissions) ? $permissions : [];
    }

    public function actionRolePermissions()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $role = Yii::$app->request->get('role');
        if (!$role) {
            return ['error' => Yii::t('app', 'Rol nomi kerak.')];
        }

        $permissions = ApiClient::request('GET', '/v1/permission/by-role?role=' . urlencode($role));

        return is_array($permissions) ? $permissions : [];
    }

    public function actionUpdateRolePermissionsApi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return ['error' => Yii::t('app', 'Sizga bu amalni bajarishga ruxsat yo‘q.')];
        }

        $body = Yii::$app->request->bodyParams;
        $role = $body['role'] ?? null;
        $permissions = $body['permissions'] ?? null;

        if (!$role || !is_array($permissions)) {
            return ['error' => Yii::t('app', 'Rol va permissionlar ko‘rsatilmagan.')];
        }

        $response = ApiClient::request('PUT', '/v1/permission/update-role-permissions', [
            'role' => $role,
            'permissions' => $permissions,
        ]);

        if (is_array($response) && isset($response['message'])) {
            Yii::$app->response->statusCode = 200; // ✅ Success uchun majburiy
            return $response;
        }

        // Error bo‘lsa
        Yii::$app->response->statusCode = 400;
        return ['error' => Yii::t('app', 'Permissionlarni yangilashda xatolik yuz berdi.')];
    }

    public function actionCreateRoleApi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return ['error' => Yii::t('app', 'Sizga bu amalni bajarishga ruxsat yo‘q.')];
        }

        $body = Yii::$app->request->bodyParams;
        $name = $body['name'] ?? null;

        if (!$name) {
            return ['error' => Yii::t('app', 'Rol nomi kerak.')];
        }

        $response = ApiClient::request('POST', '/v1/role/create', [
            'name' => $name,
        ]);

        if (is_array($response) && isset($response['message'])) {
            return ['success' => Yii::t('app', $response['message'])];
        }

        return ['error' => Yii::t('app', 'Rol yaratishda xatolik yuz berdi.')];
    }

    public function actionDeleteRoleApi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return ['error' => Yii::t('app', 'Sizga bu amalni bajarishga ruxsat yo‘q.')];
        }

        $body = Yii::$app->request->bodyParams;
        $name = $body['name'] ?? null;

        if (!$name) {
            return ['error' => Yii::t('app', 'Rol nomi kerak.')];
        }

        $response = ApiClient::request('DELETE', '/v1/role/delete', [
            'name' => $name,
        ]);

        if (is_array($response) && isset($response['message'])) {
            return ['success' => Yii::t('app', $response['message'])];
        }

        return ['error' => Yii::t('app', 'Rolni o‘chirishda xatolik yuz berdi.')];
    }

    public function actionAssignMultiplePermissionApi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return ['error' => Yii::t('app', 'Sizga bu amalni bajarishga ruxsat yo‘q.')];
        }

        $role = Yii::$app->request->post('role');
        $permissions = Yii::$app->request->post('permissions');

        if (!$role || !is_array($permissions)) {
            return ['error' => Yii::t('app', 'Rol va permissionlar ko‘rsatilmagan.')];
        }

        return ApiClient::request('POST', '/v1/permission/assign-multiple-to-role', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function actionRemoveMultiplePermissionApi()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return ['error' => Yii::t('app', 'Sizga bu amalni bajarishga ruxsat yo‘q.')];
        }

        $role = Yii::$app->request->post('role');
        $permissions = Yii::$app->request->post('permissions');

        if (!$role || !is_array($permissions)) {
            return ['error' => Yii::t('app', 'Rol va permissionlar to‘g‘ri ko‘rsatilmagan.')];
        }

        return ApiClient::request('POST', '/v1/permission/remove-multiple-from-role', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }


    public function actionAdminSetting()
    {
        if (!$this->hasRole('admin') && !$this->hasRole('creator')) {
            return $this->redirect(['dashboard/forbidden']);
        }

        $model = Setting::findOne(1);
        if (!$model) {
            $model = new Setting();
        }

        if (Yii::$app->request->isPost) {
            $oldLogo = $model->logo;
            $oldLogoBottom = $model->logo_bottom;
            $oldFavicon = $model->favicon;
            $oldOpenGraphPhoto = $model->open_graph_photo;

            $model->load(Yii::$app->request->post());

            $model->logo1 = UploadedFile::getInstance($model, 'logo1');
            $model->logo_bottom1 = UploadedFile::getInstance($model, 'logo_bottom1');
            $model->favicon1 = UploadedFile::getInstance($model, 'favicon1');
            $model->open_graph_photo1 = UploadedFile::getInstance($model, 'open_graph_photo1');

            $uploadDir = Yii::getAlias('@frontend/web/uploads/');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($model->validate()) {
                // Fayllar uchun umumiy yuklash funksiyasi
                $model->logo = $this->handleFileUpload($model->logo1, $oldLogo, $uploadDir, 'logo');
                $model->logo_bottom = $this->handleFileUpload($model->logo_bottom1, $oldLogoBottom, $uploadDir, 'logo_bottom');
                $model->favicon = $this->handleFileUpload($model->favicon1, $oldFavicon, $uploadDir, 'favicon');
                $model->open_graph_photo = $this->handleFileUpload($model->open_graph_photo1, $oldOpenGraphPhoto, $uploadDir, 'og');

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Sozlamalar muvaffaqiyatli saqlandi.'));
                    return $this->redirect(['admin-setting']);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Sozlamalarni saqlashda xatolik yuz berdi.'));
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Maʼlumotlarni toʻgʻri toʻldiring.'));
            }
        }

        return $this->render('admin/setting/index', [
            'model' => $model,
        ]);
    }

    /**
     * Faylni yuklash va eski faylni o‘chirish.
     *
     * @param UploadedFile|null $uploadedFile
     * @param string|null $oldFile
     * @param string $uploadDir
     * @param string $prefix
     * @return string|null
     */
    protected function handleFileUpload($uploadedFile, $oldFile, $uploadDir, $prefix)
    {
        if ($uploadedFile) {
            // Eski faylni o'chirish
            if ($oldFile && file_exists(Yii::getAlias('@frontend/web') . $oldFile)) {
                @unlink(Yii::getAlias('@frontend/web') . $oldFile);
            }

            // Yangi faylni saqlash
            $filename = $prefix . '_' . uniqid() . '.' . $uploadedFile->extension;
            $uploadedFile->saveAs($uploadDir . $filename);

            return '/uploads/' . $filename;
        }

        // Agar yangi fayl kelmasa eski faylni saqlab qolamiz
        return $oldFile;
    }





// ===== CATEGORY =====
    public function actionCategoryCreate()
    {
        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Yangi kategoriya qo‘shish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kategoriyalar'),
            'url'   => ['category-index']
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Category();

        if ($this->saveWithUpload($model, 'categories') && $model->id) {
            PermissionHelper::assignCoursePermissions('category', $model->id, $this->user);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Kategoriya muvaffaqiyatli yaratildi.'));
            return $this->redirect(['dashboard/category-index', 'id' => $model->id]);
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Kategoriya yaratishda xatolik yuz berdi.'));
        }

        return $this->render('category/create', compact('model'));
    }
    public function actionCategoryUpdate($id)
    {
        $model = Category::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Kategoriyani tahrirlash: {name}', ['name' => $model->getTitle()]);
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kategoriyalar'),
            'url'   => ['category-index']
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        if ($this->saveWithUpload($model, 'categories')) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Kategoriya muvaffaqiyatli yangilandi.'));
            return $this->refresh();
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Kategoriya yangilashda xatolik yuz berdi.'));
        }

        return $this->render('category/update', compact('model'));
    }
    public function actionCategoryDelete($id)
    {
        if ($model = Category::findOne($id)) {
            $deletedFile = false;

            if ($model->picture && file_exists(Yii::getAlias('@frontend/web/') . $model->picture)) {
                $deletedFile = @unlink(Yii::getAlias('@frontend/web/') . $model->picture);
            }

            $model->delete();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Kategoriya o‘chirildi.') .
                ($deletedFile ? '' : ' ' . Yii::t('app', 'Rasm topilmadi yoki o‘chirilmadi.')));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Kategoriya topilmadi.'));
        }

        return $this->redirect(['dashboard/category-index']);
    }
    public function actionCategoryIndex()
    {
        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Kategoriyalar');
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Category();
        $models = Category::find()->orderBy(['position' => SORT_ASC])->all();

        return $this->render('category/index', compact('model', 'models'));
    }
    public function actionCategoryPosition()
    {
        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Kategoriyalar tartibini o‘zgartirish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kategoriyalar'),
            'url'   => ['category-index']
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Category();
        $models = Category::find()->orderBy(['position' => SORT_ASC])->all();

        return $this->render('category/position', compact('model', 'models'));
    }




    // ===== COURSE =====
    public function actionCourseCreate($category_id)
    {
        $category = Category::findOne($category_id);
        if (!$category) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Yangi kurs qo‘shish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kategoriyalar'),
            'url'   => ['category-index']
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kurslar'),
            'url'   => ['course-index', 'category_id' => $category_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Course();
        $model->category_id = $category_id;

        if ($this->saveWithUpload($model, 'courses')) {
            PermissionHelper::assignCoursePermissions('course', $model->id, $this->user);
            return $this->redirect(['dashboard/course-update', 'id' => $model->id]);
        }

        return $this->render('course/create', compact('model', 'category'));
    }
    public function actionCourseUpdate($id)
    {
        $model = Course::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $category = $model->category;

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Kursni tahrirlash: {name}', ['name' => $model->getTitle()]);
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kategoriyalar'),
            'url'   => ['category-index']
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kurslar'),
            'url'   => ['course-index', 'category_id' => $category->id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        if ($this->saveWithUpload($model, 'courses')) {
            return $this->refresh();
        }

        return $this->render('course/update', compact('model', 'category'));
    }
    public function actionCourseDelete($id)
    {
        if ($model = Course::findOne($id)) {
            $model->delete();
        }
        return $this->redirect(['dashboard/course-index', 'category_id' => $model->category_id]);
    }
    public function actionCourseIndex($category_id)
    {
        $category = Category::findOne($category_id);
        if (!$category) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Kurslar');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kategoriyalar'),
            'url'   => ['category-index']
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Course();
        $models = Course::find()
            ->where(['category_id' => $category_id])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return $this->render('course/index', compact('model', 'models', 'category_id', 'category'));
    }
    public function actionCoursePosition($category_id)
    {
        $category = Category::findOne($category_id);
        if (!$category) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Kurslar tartibini o‘zgartirish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kategoriyalar'),
            'url'   => ['category-index']
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kurslar'),
            'url'   => ['course-index', 'category_id' => $category_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Course();
        $models = Course::find()
            ->where(['category_id' => $category_id])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return $this->render('course/position', compact('model', 'models', 'category_id', 'category'));
    }
    public function actionCourseAll()
    {

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Barcha Kurslar');
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $models = Course::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->render('course/all', compact( 'models', ));
    }


    // ===== LESSON =====
    public function actionLessonCreate($course_id)
    {
        $course = Course::findOne($course_id);
        if (!$course) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Yangi dars qo‘shish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kurslar'),
            'url'   => ['course-index', 'category_id' => $course->category_id]
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Darslar'),
            'url'   => ['lesson-index', 'course_id' => $course_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Lesson();
        $model->course_id = $course_id;

        if ($this->saveWithUpload($model, 'lessons')) {
            PermissionHelper::assignCoursePermissions('lesson', $model->id, $this->user);
            return $this->redirect(['dashboard/lesson-update', 'id' => $model->id]);
        }

        return $this->render('lesson/create', compact('model', 'course'));
    }
    public function actionLessonUpdate($id)
    {
        $model = Lesson::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $course = $model->course;

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Darsni tahrirlash: {name}', ['name' => $model->getTitle()]);
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kurslar'),
            'url'   => ['course-index', 'category_id' => $course->category_id]
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Darslar'),
            'url'   => ['lesson-index', 'course_id' => $course->id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        if ($this->saveWithUpload($model, 'lessons')) {
            return $this->refresh();
        }

        return $this->render('lesson/update', compact('model', 'course'));
    }
    public function actionLessonDelete($id)
    {
        if ($model = Lesson::findOne($id)) {
            $model->delete();
        }
        return $this->redirect(['dashboard/lesson-index', 'course_id' => $model->course_id]);
    }
    public function actionLessonIndex($course_id)
    {
        $course = Course::findOne($course_id);
        if (!$course) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Darslar');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kurslar'),
            'url'   => ['course-index', 'category_id' => $course->category_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Lesson();
        $models = Lesson::find()
            ->where(['course_id' => $course_id])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return $this->render('lesson/index', compact('model', 'models', 'course_id', 'course'));
    }
    public function actionLessonPosition($course_id)
    {
        $course = Course::findOne($course_id);
        if (!$course) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Darslar tartibini o‘zgartirish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Kurslar'),
            'url'   => ['course-index', 'category_id' => $course->category_id]
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Darslar'),
            'url'   => ['lesson-index', 'course_id' => $course_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Lesson();
        $models = Lesson::find()
            ->where(['course_id' => $course_id])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return $this->render('lesson/position', compact('model', 'models', 'course_id', 'course'));
    }
    public function actionLessonAll()
    {

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Barcha darslar');
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $models = Lesson::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->render('lesson/all', compact( 'models', ));
    }


    // ===== PART =====
    public function actionPartCreate($lesson_id)
    {
        $lesson = Lesson::findOne($lesson_id);
        if (!$lesson) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumblar
        $this->view->title = Yii::t('app', 'Yangi qism qo‘shish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Darslar'),
            'url'   => ['lesson-index', 'course_id' => $lesson->course_id]
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Qismlar'),
            'url'   => ['part-index', 'lesson_id' => $lesson_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Part();
        $model->lesson_id = $lesson_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Qism muvaffaqiyatli yaratildi.'));
            return $this->redirect(['part-index', 'lesson_id' => $lesson_id]);
        }

        return $this->render('part/create', compact('model'));
    }
    public function actionPartUpdate($id)
    {
        $model = Part::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $lesson = $model->lesson;

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Qismni tahrirlash: {name}', ['name' => $model->getTitle()]);
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Darslar'),
            'url'   => ['lesson-index', 'course_id' => $lesson->course_id]
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Qismlar'),
            'url'   => ['part-index', 'lesson_id' => $lesson->id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        // ✅ POST requestni qabul qilish va modelni saqlash
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Qism muvaffaqiyatli yangilandi.'));
            return $this->redirect(['part-index', 'lesson_id' => $lesson->id]);
        }

        return $this->render('part/update', compact('model'));
    }

    public function actionPartDelete($id)
    {
        if ($model = Part::findOne($id)) {
            $model->delete();
        }
        return $this->redirect(['dashboard/part-index', 'lesson_id' => $model->lesson_id]);
    }
    public function actionPartIndex($lesson_id)
    {
        $lesson = Lesson::findOne($lesson_id);
        if (!$lesson) {
            throw new NotFoundHttpException();
        }

        // Breadcrumb'larni sozlash
        $this->view->title = Yii::t('app', 'Qismlar');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Dars'),
            'url'   => ['lesson-index', 'course_id' => $lesson->course_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Part();
        $models = Part::find()
            ->where(['lesson_id' => $lesson_id])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return $this->render('part/index', compact('model', 'models', 'lesson_id'));
    }
    public function actionPartPosition($lesson_id)
    {
        $lesson = Lesson::findOne($lesson_id);
        if (!$lesson) {
            throw new NotFoundHttpException();
        }

        // ✅ Breadcrumblar
        $this->view->title = Yii::t('app', 'Qismlar tartibini o‘zgartirish');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Darslar'),
            'url'   => ['lesson-index', 'course_id' => $lesson->course_id]
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('app', 'Qismlar'),
            'url'   => ['part-index', 'lesson_id' => $lesson_id]
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = new Part();
        $models = Part::find()
            ->where(['lesson_id' => $lesson_id])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        return $this->render('part/position', compact('model', 'models', 'lesson_id'));
    }
    public function actionPartAll()
    {

        // ✅ Breadcrumb'lar
        $this->view->title = Yii::t('app', 'Barcha qismlar');
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $models = Part::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->render('part/all', compact( 'models', ));
    }

    private function saveWithUpload($model, $folder = null)
    {
        if (Yii::$app->request->isPost) {
            $oldPicture = $model->picture; // eski rasmni eslab qolamiz
            $model->load(Yii::$app->request->post());
            $model->picture = UploadedFile::getInstance($model, 'picture');

            if ($model->validate()) {
                if ($model->picture && $folder) {
                    // 🔥 Eski rasmni o‘chiramiz (agar mavjud bo‘lsa)
                    if ($oldPicture && file_exists(Yii::getAlias('@frontend/web/') . $oldPicture)) {
                        @unlink(Yii::getAlias('@frontend/web/') . $oldPicture);
                    }

                    // ✅ Yangi rasmni saqlaymiz
                    $filename = uniqid() . '.' . $model->picture->extension;
                    $path = Yii::getAlias("@frontend/web/uploads/{$folder}/" . $filename);
                    if ($model->picture->saveAs($path)) {
                        $model->picture = "uploads/{$folder}/" . $filename;
                    }
                } else {
                    // ⚠️ Rasm o‘zgartirilmagan bo‘lsa, eski rasmni saqlab qolamiz
                    $model->picture = $oldPicture;
                }

                return $model->save(false);
            }
        }
        return false;
    }
    public function actionUpdatePosition($model)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $order = Yii::$app->request->post('order', []);
        $modelClass = 'common\\models\\' . ucfirst($model); // masalan: 'category' → 'common\models\Category'

        return PositionHelper::saveOrder($modelClass, $order);
    }




//PART CONTENT
    public function actionPartContentCreate($part_id)
    {
        $part = Part::findOne($part_id);
        if (!$part) {
            throw new NotFoundHttpException(Yii::t('app', 'Bo‘lim topilmadi.'));
        }

        $this->view->title = Yii::t('app', 'Kontent qo‘shish');

        $model = new PartContent();
        $model->part_id = $part_id;

        $contentModel = null;

        $model->position = PartContent::find()->where(['part_id' => $part_id])->max('position') + 1;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->user_id = Yii::$app->session->get('user_profile')['id'] ?? null;
            if ($model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save(false)) {
                        $contentModel = $this->createContentModelInstance($model->type_id);
                        if ($contentModel) {
                            $contentModel->load(Yii::$app->request->post());

                            // Fayl yuklash
                            if ($contentModel->hasAttribute('file_path')) {
                                $file = UploadedFile::getInstance($contentModel, 'file_path');
                                if ($file) {
                                    $dir = Yii::getAlias('@frontend/web/uploads/parts/' . $model->id);
                                    if (!is_dir($dir)) {
                                        mkdir($dir, 0775, true);
                                    }
                                    $filename = uniqid() . '.' . $file->extension;
                                    $filePath = $dir . '/' . $filename;
                                    if ($file->saveAs($filePath)) {
                                        $contentModel->file_path = '/uploads/parts/' . $model->id . '/' . $filename;
                                    } else {
                                        Yii::error("Faylni saqlab bo‘lmadi: $filePath", __METHOD__);
                                    }
                                }
                            }

                            $contentModel->part_content_id = $model->id; // ✅ MUHIM!
                            if ($contentModel->save(false)) {
                                $transaction->commit();
                                $this->flashSuccess(Yii::t('app', 'Kontent qo‘shildi.'));
                                return $this->redirect(['dashboard/part-content-index', 'part_id' => $part_id]);
                            } else {
                                Yii::error('Kontent modelni saqlab bo‘lmadi', __METHOD__);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error("Kontent yaratishda xatolik: " . $e->getMessage(), __METHOD__);
                    $this->flashError(Yii::t('app', 'Kontentni saqlashda xatolik yuz berdi.'));
                }
            } else {
                Yii::error("Validatsiya xatolik: " . VarDumper::dumpAsString($model->getErrors()), __METHOD__);
            }
        }

        return $this->render('part-content/create', [
            'model' => $model,
            'part' => $part,
            'contentModel' => $contentModel,
        ]);
    }
    /**
     * @throws NotFoundHttpException
     */
    public function actionPartContentUpdate($id)
    {
        $model = PartContent::find()
            ->with(['type', 'textContent', 'pictureContent', 'videoContent', 'pdfContent'])
            ->where(['id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'Kontent topilmadi.'));
        }

        $part = $model->part;
        $this->view->title = Yii::t('app', 'Kontentni tahrirlash');

        $contentModel = $model->getActiveContent();
        if (!$contentModel) {
            Yii::error("ActiveContent topilmadi: type_id = {$model->type_id}", __METHOD__);
            throw new NotFoundHttpException(Yii::t('app', 'Ushbu kontentga mos model topilmadi.'));
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $contentModel->load(Yii::$app->request->post());

            $isFileUploaded = false;

            // Fayl mavjud bo‘lsa, yuklab olishga tayyorlaymiz
            if ($contentModel->hasAttribute('file_path')) {
                $file = UploadedFile::getInstance($contentModel, 'file_path');
                if ($file) {
                    $dir = Yii::getAlias('@frontend/web/uploads/parts/' . $model->id);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    $filename = uniqid() . '.' . $file->extension;
                    $path = $dir . '/' . $filename;
                    if ($file->saveAs($path)) {
                        $contentModel->file_path = '/uploads/parts/' . $model->id . '/' . $filename;
                        $isFileUploaded = true;
                    } else {
                        Yii::error("Faylni saqlab bo‘lmadi: $path", __METHOD__);
                    }
                }
            }

            if ($model->validate() && $contentModel->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->save(false);
                    $contentModel->save(false);
                    $transaction->commit();

                    $this->flashSuccess(Yii::t('app', 'Kontent yangilandi.'));
                    return $this->refresh();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error("Yangilashda xatolik: " . $e->getMessage(), __METHOD__);
                    $this->flashError(Yii::t('app', 'Xatolik yuz berdi.'));
                }
            } else {
                Yii::error("Validatsiya xatolik: " .
                    VarDumper::dumpAsString($model->getErrors()) . "\n" .
                    VarDumper::dumpAsString($contentModel->getErrors()), __METHOD__);
            }
        }

        return $this->render('part-content/update', [
            'model' => $model,
            'part' => $part,
            'contentModel' => $contentModel,
        ]);
    }
    public function actionLoadContentFormByType($type_id): string
    {
        $type = ContentType::findOne($type_id);
        if (!$type) {
            return 'Invalid type';
        }

        switch ($type->name) {
            case 'text':
                $content = new ContentText();
                return $this->renderPartial('//dashboard/part-content/forms/_form_text', ['model' => $content]);
            case 'picture':
                $content = new ContentPicture();
                return $this->renderPartial('//dashboard/part-content/forms/_form_picture', ['model' => $content]);
            case 'video':
                $content = new ContentVideo();
                return $this->renderPartial('//dashboard/part-content/forms/_form_video', ['model' => $content]);
            case 'pdf':
                $content = new ContentPdf();
                return $this->renderPartial('//dashboard/part-content/forms/_form_pdf', ['model' => $content]);
            default:
                return '';
        }
    }
    /**
     * Turiga qarab kontent modelini yaratish.
     */
    private function createContentModelInstance($typeId)
    {
        $type = ContentType::findOne($typeId);
        if (!$type) {
            return null;
        }

        return match ($type->name) {
            'text' => new \common\models\ContentText(),
            'picture' => new \common\models\ContentPicture(),
            'video' => new \common\models\ContentVideo(),
            'pdf' => new \common\models\ContentPdf(),
            default => null,
        };
    }

    private function createContentByType(PartContent $model)
    {
        $contentModel = match ($model->type->name) {
            'text' => new ContentText(),
            'picture' => new ContentPicture(),
            'video' => new ContentVideo(),
            'pdf' => new ContentPdf(),
            default => null,
        };

        if (!$contentModel) {
            return false;
        }

        $contentModel->part_content_id = $model->id;
        return $contentModel->save(false);
    }
    /**
     * @throws NotFoundHttpException
     */
    public function actionPartContentDelete($id): \yii\web\Response
    {
        $model = PartContent::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        $partId = $model->part_id;

        try {
            $model->delete();
            $this->flashSuccess(Yii::t('app', 'Kontent o‘chirildi.'));
        } catch (\Throwable $e) {
            Yii::error($e->getMessage());
            $this->flashError(Yii::t('app', 'Kontentni o‘chirishda xatolik.'));
        }

        return $this->redirect(['dashboard/part-content-index', 'part_id' => $partId]);
    }
    public function actionPartContentIndex($part_id): string
    {
        $part = Part::find()
            ->with(['lesson.course']) // Eager loading
            ->where(['id' => $part_id])
            ->one();

        if (!$part) {
            throw new NotFoundHttpException();
        }

        $course = $part->lesson->course ?? null;
        $lesson = $part->lesson ?? null;

        $this->view->title = Yii::t('app', 'Qism kontentlari');
        if ($course) {
            $this->view->params['breadcrumbs'][] = [
                'label' => Html::encode($course->getTitle()),
                'url' => ['lesson-index', 'course_id' => $course->id]
            ];
        }
        if ($lesson) {
            $this->view->params['breadcrumbs'][] = [
                'label' => Html::encode($lesson->getTitle()),
                'url' => ['part-index', 'lesson_id' => $lesson->id]
            ];
        }

        $this->view->params['breadcrumbs'][] = Html::encode($part->getTitle());
        $this->view->params['breadcrumbs'][] = $this->view->title;

        // 🟢 MUHIM: Eager loading qilish shart
        $dataProvider = new ActiveDataProvider([
            'query' => PartContent::find()
                ->where(['part_id' => $part_id])
                ->with(['type', 'textContent', 'pictureContent', 'videoContent', 'pdfContent'])
                ->orderBy(['position' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('part-content/index', [
            'dataProvider' => $dataProvider,
            'part' => $part,
        ]);
    }

    public function actionPartContentSort(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids', []);
        foreach ($ids as $position => $id) {
            PartContent::updateAll(['position' => $position], ['id' => $id]);
        }
        return ['success' => true];
    }
    /**
     * @throws Exception
     */
    public function actionPartContentToggleStatus(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');

        if (($model = PartContent::findOne($id)) !== null) {
            $model->status = (int)$status;
            $model->save(false);
            return ['success' => true];
        }
        return ['success' => false];
    }
    /**
     * @throws NotFoundHttpException
     */
    public function actionPartContentPreview($id): string
    {
        $model = PartContent::find()
            ->with(['type', 'textContent', 'pictureContent', 'videoContent', 'pdfContent'])
            ->where(['id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('part-content/preview', [
            'model' => $model,
            'content' => $model->getActiveContent(), // yoki $model->activeContent — agar getter ishlasa
        ]);
    }

    protected function findPartContentModel($id)
    {
        $model = PartContent::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Kontent topilmadi.');
        }

        $type = $model->type; // Masalan: 'text'

        $modelClass = $this->modelClassForType($type);

        if ($modelClass && class_exists($modelClass)) {
            $contentModel = $modelClass::findOne(['part_content_id' => $model->id]);
            if ($contentModel) {
                return $contentModel;
            }
        }

        Yii::error("ActiveContent topilmadi: type = " . $modelClass, __METHOD__);
        throw new NotFoundHttpException('Ushbu kontentga mos model topilmadi.');
    }
    private function modelClassForType($type)
    {
        switch ($type) {
            case 'text':
                return \common\models\ContentText::class;
            case 'image':
                return \common\models\ContentPicture::class;
            case 'video':
                return \common\models\ContentVideo::class;
            case 'pdf':
                return \common\models\ContentPdf::class;
            default:
                return null;
        }
    }




//QUIZ

    /**
     * @throws NotFoundHttpException
     */
    public function actionQuizIndex($part_id): string
    {
        $part = Part::findOne($part_id);
        if (!$part) {
            throw new NotFoundHttpException('Part topilmadi.');
        }

        $quizzes = Quiz::find()->where(['part_id' => $part_id])->orderBy(['id' => SORT_DESC])->all();

        return $this->render('quiz/index', [
            'part' => $part,
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionQuizCreate($part_id, $type = 1): Response|string
    {
        $part = Part::findOne($part_id);
        if (!$part) {
            throw new NotFoundHttpException('Part topilmadi.');
        }

        $model = new Quiz();
        $model->part_id = $part_id;
        $model->type = $type;
        $model->time_limit = 15;
        $model->pass_percent = 60;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Quiz yaratildi.');
            return $this->redirect(['quiz-index', 'part_id' => $part_id]);
        }

        return $this->render('quiz/form', [
            'model' => $model,
            'part' => $part,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionQuizUpdate($id): Response|string
    {
        $model = Quiz::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Quiz topilmadi.');
        }

        $part = $model->part;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Quiz yangilandi.');
            return $this->redirect(['quiz-index', 'part_id' => $part->id]);
        }

        return $this->render('quiz/form', [
            'model' => $model,
            'part' => $part,
        ]);
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionQuizDelete($id): Response
    {
        $model = Quiz::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Quiz topilmadi.');
        }

        $part_id = $model->part_id;
        $model->delete();

        Yii::$app->session->setFlash('success', 'Quiz o‘chirildi.');
        return $this->redirect(['quiz-index', 'part_id' => $part_id]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionQuestionManage($quiz_id): string
    {
        $quiz = Quiz::findOne($quiz_id);
        if (!$quiz) {
            throw new NotFoundHttpException('Quiz topilmadi.');
        }

        $questions = $quiz->questions;

        return $this->render('question/index', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionQuestionCreate($quiz_id): Response|string
    {
        $quiz = Quiz::findOne($quiz_id);
        if (!$quiz) {
            throw new NotFoundHttpException('Quiz topilmadi.');
        }

        $model = new QuizQuestion();
        $model->quiz_id = $quiz_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Savol qo‘shildi.');
            return $this->redirect(['question-manage', 'quiz_id' => $quiz_id]);
        }

        return $this->render('question/form', [
            'model' => $model,
            'quiz' => $quiz,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionQuestionUpdate($id): Response|string
    {
        $model = QuizQuestion::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Savol topilmadi.');
        }

        $quiz = $model->quiz;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Savol yangilandi.');
            return $this->redirect(['question-manage', 'quiz_id' => $quiz->id]);
        }

        return $this->render('question/form', [
            'model' => $model,
            'quiz' => $quiz,
        ]);
    }

    /**
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws NotFoundHttpException
     */
    public function actionQuestionDelete($id): Response
    {
        $model = QuizQuestion::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Savol topilmadi.');
        }

        $quiz_id = $model->quiz_id;
        $model->delete();

        Yii::$app->session->setFlash('success', 'Savol o‘chirildi.');
        return $this->redirect(['question-manage', 'quiz_id' => $quiz_id]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionOptionManage($question_id): string
    {
        $question = QuizQuestion::findOne($question_id);
        if (!$question) {
            throw new NotFoundHttpException('Savol topilmadi.');
        }

        $options = $question->options;

        return $this->render('option/index', [
            'question' => $question,
            'options' => $options,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionOptionCreate($question_id): Response|string
    {
        $question = QuizQuestion::findOne($question_id);
        if (!$question) {
            throw new NotFoundHttpException('Savol topilmadi.');
        }

        $model = new QuizOption();
        $model->question_id = $question_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Variant qo‘shildi.');
            return $this->redirect(['option-manage', 'question_id' => $question_id]);
        }

        return $this->render('option/form', [
            'model' => $model,
            'question' => $question,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionOptionUpdate($id): Response|string
    {
        $model = QuizOption::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Variant topilmadi.');
        }

        $question = $model->question;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Variant yangilandi.');
            return $this->redirect(['option-manage', 'question_id' => $question->id]);
        }

        return $this->render('option/form', [
            'model' => $model,
            'question' => $question,
        ]);
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionOptionDelete($id): Response
    {
        $model = QuizOption::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Variant topilmadi.');
        }

        $question_id = $model->question_id;
        $model->delete();

        Yii::$app->session->setFlash('success', 'Variant o‘chirildi.');
        return $this->redirect(['option-manage', 'question_id' => $question_id]);
    }


    /**
     * @throws NotFoundHttpException
     */
    public function actionEssayCriteriaManage($quiz_id): string
    {
        $quiz = Quiz::findOne($quiz_id);
        if (!$quiz || $quiz->type != 2) {
            throw new NotFoundHttpException("Quiz topilmadi yoki essay turida emas.");
        }

        $criteriaList = EssayCriteria::find()->where(['quiz_id' => $quiz_id])->orderBy(['id' => SORT_ASC])->all();

        return $this->render('essay-criteria/index', [
            'quiz' => $quiz,
            'criteriaList' => $criteriaList,
        ]);
    }


    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionEssayCriteriaCreate($quiz_id): Response|string
    {
        $quiz = Quiz::findOne($quiz_id);
        if (!$quiz || $quiz->type != 2) {
            throw new NotFoundHttpException("Quiz topilmadi yoki essay turida emas.");
        }

        $model = new EssayCriteria();
        $model->quiz_id = $quiz_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Mezon qo‘shildi.");
            return $this->redirect(['essay-criteria-manage', 'quiz_id' => $quiz_id]);
        }

        return $this->render('essay-criteria/form', [
            'model' => $model,
            'quiz' => $quiz,
        ]);
    }


    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionEssayCriteriaUpdate($id): Response|string
    {
        $model = EssayCriteria::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Mezon topilmadi.");
        }

        $quiz = $model->quiz;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Mezon yangilandi.");
            return $this->redirect(['essay-criteria-manage', 'quiz_id' => $quiz->id]);
        }

        return $this->render('essay-criteria/form', [
            'model' => $model,
            'quiz' => $quiz,
        ]);
    }

    /**
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws NotFoundHttpException
     */
    public function actionEssayCriteriaDelete($id): Response
    {
        $model = EssayCriteria::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Mezon topilmadi.");
        }

        $quiz_id = $model->quiz_id;
        $model->delete();

        Yii::$app->session->setFlash('success', "Mezon o‘chirildi.");
        return $this->redirect(['essay-criteria-manage', 'quiz_id' => $quiz_id]);
    }



}
