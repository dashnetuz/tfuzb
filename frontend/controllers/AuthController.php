<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\httpclient\Client;
use frontend\models\LoginForm;
use frontend\models\RegisterForm;
use frontend\models\RequestPasswordResetForm;
use frontend\models\ResetPasswordForm;

class AuthController extends Controller
{
    public $layout = 'auth';

    private function postToApi($url, $data)
    {
        $client = new Client();
        return $client->createRequest()
            ->setMethod('POST')
            ->setUrl(Yii::$app->params['apiBaseUrl'] . $url) // <-- MUHIM
            ->addHeaders(['Content-Type' => 'application/json'])
            ->setContent(json_encode($data))
            ->send();
    }

    private function getProfile($token)
    {
        $client = new Client();
        return $client->createRequest()
            ->setMethod('GET')
            ->setUrl(Yii::$app->params['apiBaseUrl'] . '/v1/user/profile') // <-- MUHIM
            ->addHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])
            ->send();
    }

    public function actionLogin()
    {
        if (Yii::$app->session->has('user_token') && Yii::$app->session->has('user_profile')) {
            return $this->redirect(['/dashboard']);
        }

        $model = new LoginForm();
        $error = Yii::$app->session->getFlash('error');
        $success = Yii::$app->session->getFlash('success');

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $response = $this->postToApi('/v1/auth/login', [
                    'username' => $model->username,
                    'password' => $model->password,
                ]);

                if ($response->isOk && !empty($response->data['token'])) {
                    Yii::$app->session->set('user_token', $response->data['token']);

                    $profileResponse = $this->getProfile($response->data['token']);
                    if ($profileResponse->isOk) {
                        Yii::$app->session->set('user_profile', $profileResponse->data);
                        return $this->redirect(['/dashboard/index']);
                    } else {
                        $error = $profileResponse->data['error'] ?? 'Profilni olishda xatolik.';
                    }
                } else {
                    $error = $response->data['error'] ?? 'Login xatolik yuz berdi.';
                }
            } catch (\Throwable $e) {
                $error = 'Server bilan aloqa xatosi.';
            }
        }

        return $this->render('login', [
            'model' => $model,
            'error' => $error,
            'success' => $success,
        ]);
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        $error = null;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $response = $this->postToApi('/v1/auth/register', [
                    'username' => $model->username,
                    'email' => $model->email,
                    'password' => $model->password,
                ]);

                if ($response->isOk) {
                    Yii::$app->session->setFlash('success', 'Roʻyxatdan o‘tish muvaffaqiyatli yakunlandi. Endi login qilishingiz mumkin.');
                    return $this->redirect(['auth/login']);
                } else {
                    $error = $response->data['error'] ?? 'Ro‘yxatdan o‘tishda xatolik.';
                }
            } catch (\Throwable $e) {
                $error = 'Server bilan aloqa xatosi.';
            }
        }

        return $this->render('register', [
            'model' => $model,
            'error' => $error,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new RequestPasswordResetForm();
        $error = null;
        $success = null;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $response = $this->postToApi('/v1/auth/request-password-reset', [
                    'email' => $model->email,
                ]);

                if ($response->isOk) {
                    $success = 'Parolni tiklash uchun ko‘rsatmalar yuborildi.';
                } else {
                    $error = $response->data['error'] ?? 'Xatolik yuz berdi.';
                }
            } catch (\Throwable $e) {
                $error = 'Server bilan aloqa xatosi.';
            }
        }

        return $this->render('request-password-reset', [
            'model' => $model,
            'error' => $error,
            'success' => $success,
        ]);
    }

    public function actionResetPassword($token)
    {
        $model = new ResetPasswordForm();
        $error = null;
        $success = null;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $response = $this->postToApi('/v1/auth/reset-password', [
                    'token' => $token,
                    'password' => $model->password,
                ]);

                if ($response->isOk) {
                    $success = 'Parolingiz muvaffaqiyatli yangilandi.';
                } else {
                    $error = $response->data['error'] ?? 'Parolni yangilashda xatolik.';
                }
            } catch (\Throwable $e) {
                $error = 'Server bilan aloqa xatosi.';
            }
        }

        return $this->render('reset-password', [
            'model' => $model,
            'error' => $error,
            'success' => $success,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->session->remove('user_token');
        Yii::$app->session->remove('user_profile');
        return $this->redirect(['auth/login']);
    }

    /** --------- SOCIAL LOGIN (faqat Google) --------- **/

    public function actionSocialCallback($provider)
    {
        $code = Yii::$app->request->get('code');
        $error = null;

        if ($provider !== 'google' || !$code) {
            Yii::$app->session->setFlash('error', 'Provider yoki code noto‘g‘ri.');
            return $this->redirect(['auth/login']);
        }

        try {
            $response = $this->postToApi('/v1/social/login', [
                'provider' => 'google',
                'code' => $code,
            ]);

            if ($response->isOk && !empty($response->data['token'])) {
                Yii::$app->session->set('user_token', $response->data['token']);

                $profileResponse = $this->getProfile($response->data['token']);
                if ($profileResponse->isOk) {
                    Yii::$app->session->set('user_profile', $profileResponse->data);
                    return $this->redirect(['/dashboard']);
                } else {
                    $error = $profileResponse->data['error'] ?? 'Profilni olishda xatolik.';
                }
            } else {
                $error = $response->data['error'] ?? 'Social login xatolik.';
            }
        } catch (\Throwable $e) {
            $error = 'Server bilan aloqa xatosi.';
        }

        Yii::$app->session->setFlash('error', $error);
        return $this->redirect(['auth/login']);
    }
}
