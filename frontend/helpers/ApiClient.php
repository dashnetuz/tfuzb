<?php

namespace frontend\helpers;

use Yii;
use yii\httpclient\Client;

class ApiClient
{
    public static function request($method, $endpoint, $data = [], $isMultipart = false)
    {
        $token = Yii::$app->session->get('user_token');
        $baseUrl = Yii::$app->params['apiBaseUrl'];

        if (!$token) {
            self::handleSessionExpired();
        }

        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);

        $request = $client->createRequest([
            'options' => [
                CURLOPT_TIMEOUT => 30,
            ],
        ])
            ->setMethod($method)
            ->setUrl($baseUrl . $endpoint)
            ->addHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ]);

        if ($isMultipart) {
            foreach ($data as $name => $fileInfo) {
                if (is_array($fileInfo) && isset($fileInfo['tempName'])) {
                    $request->addFile($name, $fileInfo['tempName'], [
                        'fileName' => $fileInfo['name'] ?? basename($fileInfo['tempName']),
                        'mimeType' => $fileInfo['type'] ?? 'application/octet-stream',
                    ]);
                }
            }
        } else {
            if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
                $request
                    ->setContent(json_encode($data))
                    ->addHeaders(['Content-Type' => 'application/json']);
            } else {
                $request->setData($data);
            }
        }



        try {
            $response = $request->send();

            if (!$response->isOk) {
                Yii::error('API Error: HTTP ' . $response->statusCode . ' - ' . $response->content, __METHOD__);
                return self::handleApiError($response->statusCode);
            }

            return $response->data;
        } catch (\yii\httpclient\Exception $e) {
            Yii::error('API Request Exception: ' . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', Yii::t('app', 'Serverga ulanishda xatolik yuz berdi.'));
            return null;
        } catch (\Throwable $e) {
            Yii::error('Throwable Exception: ' . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', Yii::t('app', 'Nomaʼlum xatolik yuz berdi.'));
            return null;
        }
    }

    protected static function handleSessionExpired()
    {
        Yii::$app->session->destroy();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Sessiya tugagan. Iltimos, qayta login qiling.'));
        Yii::$app->response->redirect(['/auth/login'])->send();
        Yii::$app->end();
    }

    protected static function handleApiError($statusCode)
    {
        switch ($statusCode) {
            case 400:
                Yii::$app->session->setFlash('error', Yii::t('app', '400 - Noto‘g‘ri so‘rov (Bad Request).'));
                break;
            case 401: // ❗ 401 bo'lsa logout qilamiz
                self::handleSessionExpired();
                break;
            case 403: // ❗ 403 bo'lsa FAKAT alert chiqaramiz, logout qilmaymiz
                Yii::$app->session->setFlash('error', Yii::t('app', '403 - Ruxsat berilmagan.'));
                break;
            case 404:
                Yii::$app->session->setFlash('error', Yii::t('app', '404 - Resurs topilmadi.'));
                break;
            case 422:
                Yii::$app->session->setFlash('error', Yii::t('app', '422 - Tasdiqlash (validation) xatoligi.'));
                break;
            case 500:
                Yii::$app->session->setFlash('error', Yii::t('app', '500 - Serverdagi ichki xatolik.'));
                break;
            case 502:
                Yii::$app->session->setFlash('error', Yii::t('app', '502 - Server javob bermadi (Bad Gateway).'));
                break;
            case 503:
                Yii::$app->session->setFlash('error', Yii::t('app', '503 - Xizmat vaqtincha mavjud emas.'));
                break;
            default:
                Yii::$app->session->setFlash('error', Yii::t('app', 'Xatolik yuz berdi. Kod: {code}', ['code' => $statusCode]));
                break;
        }
        return null;
    }

}
