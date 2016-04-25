<?php
/**
 * Created by PhpStorm.
 * User: gevor
 * Date: 4/20/2016
 * Time: 20:25
 */

namespace app\components;
use yii\web\Controller as BaseController;

class Controller extends BaseController
{
  public function beforeAction($action)
  {
    \app\components\SocketDaemon::setConfig(\Yii::$app->params['socketHost'], \Yii::$app->params['socketPort']);

    
     
    
    return parent::beforeAction($action);

  }
}

//