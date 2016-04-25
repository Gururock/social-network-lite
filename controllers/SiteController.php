<?php

namespace app\controllers;

use yii;
use yii\filters\AccessControl;
use app\components\Controller;
use app\components\AccessRules;
use yii\helpers\Json;
use app\models\Hobby;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [

            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRules::className(),
                    'token'=>Yii::$app->request->post('token')
                ],
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?']
                    ],

                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionHobbies()
    {
        return Json::encode((new Hobby)->getList());
    }


}
