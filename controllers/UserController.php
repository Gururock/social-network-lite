<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\RegistrationForm;
use Yii;
use yii\filters\AccessControl;
use app\components\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use app\components\AccessRules;

class UserController extends Controller
{

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRules::className(),
                    'token'=>Yii::$app->request->post('token')
                ],
                'rules' => [
                    [
                        'actions' => ['register',  'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                    'logout' => ['post'],
                    'register' => ['post'],

                ],
            ],
    ];
    }


    /**
     * Logs in a user
     * @return array
     */

    public function actionLogin()
    {
        $post = Yii::$app->request->post();

        $model = new LoginForm();

        $model->attributes = $post;
        if(($token = $model->login())){
            $response = [
                'success' => true,
                'data' => array_merge($this->findUser(Yii::$app->cache->get($token)), ['token'=>$token])
            ];
        }else{
            $errors = [];
            foreach ($model->getErrors() as $key => $error) {
                $errors[] = [
                    'field' => $key,
                    'message' => $error[0]
                ];
            }
            $response = [
                'success' => false,
                'data' => $errors
            ];
        }

        return $response;
    }

    /**
     * @return array
     * @throws ServerErrorHttpException
     */
    public function actionRegister()
    {
        /** @var  $user \app\models\User */

        $post = Yii::$app->request->post();

        $model = new RegistrationForm;

        $model->email = $post['email'];
        $model->fname = $post['fname'];
        $model->birth  = $post['birth'];
        $model->hobbies =  implode(',',Yii::$app->request->post('hobbies', []));
        $model->password = $post['password'];

        if($model->validate()){

            $user = $model->GenerateUser();

            $user->attributes = $model->attributes;
            $user->setPassword($model->password);
            if(!$user->save()){
                throw new ServerErrorHttpException('Can not create user profile. Try later');
            }

              $response = [
                'success' => true,
                'data' => []
            ];
        }else{
            $errors = [];
            foreach ($model->getErrors() as $key => $error) {
                $errors[] = [
                    'field' => $key,
                    'message' => $error[0]
                ];
            }
            $response = [
                'success' => false,
                'data' => $errors
            ];
        }

        return $response;

    }

    /**
     * Logs out the current user
     *
     * @return array
     */
    public function actionLogout()
    {

        $token = Yii::$app->request->post()['token'];

        if(Yii::$app->cache->exists($token))
        {
            Yii::$app->cache->delete($token);
        }

        return  [
            'success' => true,
            'data' => []
        ];

    }


    /**
     * Find user by authentcation key
     *
     * @param $id
     * @return string
     */
    public function findUser($id)
    {
        $connection = \Yii::$app->db;

        $model = $connection->createCommand(sprintf("SELECT * FROM user where id=:id", \app\models\User::tableName()), ['id'=>$id]);
        $model->queryOne();

        $user = $model->queryOne();
        return [
            'id' => $user['id'],
            'fname' => $user['fname']
        ];
    }

}