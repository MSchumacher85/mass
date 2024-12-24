<?php
namespace app\controllers\api;

use app\models\search\RequestSearch;
use Yii;
use yii\rest\Controller;
use app\models\Request;

class RequestController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['http://mass'], // Укажите ваш домен
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization'],
                'Access-Control-Max-Age' => 3600,
            ],
        ];
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['create'], // Заявки можно отправлять без авторизации
                    'roles' => ['?']
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'update', 'options'],
                    'roles' => ['@']
                ],
            ],
        ];
        $behaviors['contentNegotiator']['formats'] = [
            'application/json' => \yii\web\Response::FORMAT_JSON,
        ];

        return $behaviors;
    }

    public function actionCreate()
    {
        $model = new Request();
        $model->load(\Yii::$app->request->post(), '');
        if ($model->save()) {
            return ['status' => 'success', 'message' => 'Заявка успешно создана.'];
        }
        Yii::$app->response->statusCode = 422;

        return ['status' => 'error', 'errors' => $model->errors];
    }

    public function actionIndex()
    {
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $dataProvider->getModels();
    }

    public function actionUpdate($id)
    {

        $model = Request::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Заявка не найдена');
        }

        $model->load(\Yii::$app->request->bodyParams, '');

        if ($model->status === Request::STATUS_RESOLVED && empty($model->comment)) {
            return [
                'error' => 'При смене статуса на "Resolved комментарий обязателен!!!',
            ];
        }

        if ($model->save()) {
            // Если заявка переведена в статус Resolved, отправляем ответ пользователю
            if ($model->status === Request::STATUS_RESOLVED) {
                $emailSent = Yii::$app->mailer->compose()
                    ->setFrom('example@mail.ru') //TODO укажите вашу почту
                    ->setTo($model->email) // Email получателя (из модели Request)
                    ->setSubject("Ответ на вашу заявку №{$model->id}") // Тема письма
                    ->setHtmlBody("Ответ: <br>{$model->comment}") // Тело письма
                    ->send();

                if (!$emailSent) {
                    return [
                        'error' => 'Не удалось отправить письмо пользователю',
                    ];
                }
            }

            return [
                'id' => $model->id,
                'status' => $model->status,
                'comment' => $model->comment,
                'updated_at' => $model->updated_at,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return $model->errors;
    }

    public function actionOptions()
    {
        Yii::$app->response->statusCode = 200;

        Yii::$app->response->headers->add('Access-Control-Allow-Origin', 'http://mass');
        Yii::$app->response->headers->add('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS');
        Yii::$app->response->headers->add('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        Yii::$app->response->headers->add('Access-Control-Allow-Credentials', 'true');
        Yii::$app->response->headers->add('Access-Control-Max-Age', '3600');
        return;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD', 'OPTIONS'],
            'view' => ['GET', 'HEAD', 'OPTIONS'],
            'create' => ['POST', 'OPTIONS'],
            'update' => ['PUT', 'PATCH', 'OPTIONS'],
        ];
    }
}
