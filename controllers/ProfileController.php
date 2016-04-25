<?php

namespace app\controllers;

use app\components\SocketDaemon;
use app\models\Chat;
use app\models\User;
use Faker\Provider\zh_TW\DateTime;
use yii;
use yii\filters\AccessControl;
use app\components\Controller;
use yii\filters\VerbFilter;
use app\models\Friends;
use app\models\Hobby;
use app\components\AccessRules;
use yii\web\Response;

class ProfileController extends Controller
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
                        'actions' => [''],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['friends', 'potential', 'delete-friend', 'add-friend', 'upcoming-birthdays', 'birthdays', 'send-message', 'get-history'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'friends' => ['post'],
                    'potential' => ['post'],
                ],
            ],
        ];
    }

    public function actionDeleteFriend()
    {
        $token = Yii::$app->request->post('token');
        $friendId = Yii::$app->request->post('id');
        $id = Yii::$app->cache->get($token);
        $connection = Yii::$app->db;
        $query = sprintf("DELETE from %s where (accepter=:id AND requester=:friendid) OR (accepter=:friendid AND requester=:id)",  Friends::tableName());
        $success = $connection->createCommand($query, ['id'=>$id, 'friendid'=>$friendId])->execute();

        $response = [
          'success' => boolval($success),
          'data' => []
        ];
        return $response;
    }


    /**
     * Find all friends list for current user
     *
     * @return array
     */
    public function actionFriends()
    {
        $token = Yii::$app->request->post('token');

        $id = Yii::$app->cache->get($token);
        $connection = Yii::$app->db;

        $query = sprintf("SELECT u.id, u.fname, u.email, u.birth  FROM %s  f JOIN %s u on f.accepter=u.id or f.requester=u.id WHERE (f.accepter=:id or f.requester=:id) and f.approve=1 and u.id <> :id GROUP by u.id",  Friends::tableName(), User::tableName());
        $list = $connection->createCommand($query, ['id'=>$id])->queryAll();

        $response = [
            'success' => true,
            'data' => $list
        ];
        return $response;
    }



    /**
     * find a list of all users whose  birhteday is a +- 5 day of current users birthday and whoise share same hobbie
     *
     * @return array
     */
    public function actionPotential()
    {
        $token = Yii::$app->request->post('token');
        $userId = Yii::$app->cache->get($token);
        $connection = Yii::$app->db;

        $queryUser = sprintf("SELECT hobbies, birth FROM %s WHERE id = :id", User::tableName());
        $userBirth = $connection->createCommand($queryUser, ['id'=>$userId])->queryOne();

        $queryPotentialUser = sprintf("SELECT id, fname, hobbies, birth, email FROM %s WHERE birth > :datestart AND birth < :dateend AND id <> :id AND id not in(SELECT u.id FROM %s  f JOIN %s u on f.accepter=u.id or f.requester=u.id WHERE (f.accepter=:id or f.requester=:id) and f.approve=1 and u.id <> :id GROUP by u.id)", User::tableName(), Friends::tableName(), User::tableName());
        $potentialUser = $connection->createCommand($queryPotentialUser, ['datestart'=>(int)$userBirth['birth'] - (60*60*24*5), 'dateend'=>(int)$userBirth['birth'] + (60*60*24*5), 'id'=>$userId])->queryAll();

        $hobbies = explode(',', $userBirth['hobbies']);
        $userData = [];
        foreach ($potentialUser as $user){
            $result = array_intersect ($hobbies, explode(',', $user['hobbies']));
            if(!empty($result)){
                $userData[] = $user;
            }
        }

        $response = [
            'success' => true,
            'data' => $userData
        ];
        return $response;

    }

    public function actionUpcomingBirthdays()
    {
        $token = Yii::$app->request->post('token');
        $userId = Yii::$app->cache->get($token);
        $connection = Yii::$app->db;

        $query = sprintf("SELECT id, fname, hobbies, birth, email, MONTH(FROM_UNIXTIME(birth)) as m, DAY(FROM_UNIXTIME(birth)) as d FROM %s WHERE (MONTH(FROM_UNIXTIME(birth)) > MONTH(NOW()) OR (MONTH(FROM_UNIXTIME(birth)) = MONTH(NOW()) AND DAY(FROM_UNIXTIME(birth)) >= DAY(NOW()))) AND id <> :id ORDER BY m, d", User::tableName());
        $birthDays = $connection->createCommand($query, ['id'=>$userId])->queryAll();
        $response = array_map(function($value) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($value['birth']);
            $value['birthday'] = $dateTime->format('F j');
            return $value;
        }, $birthDays);

        return [
          'success' => true,
          'data' => $response
        ];
    }

    public function actionBirthdays()
    {
        $token = Yii::$app->request->post('token');
        $userId = Yii::$app->cache->get($token);
        $connection = Yii::$app->db;

        $query = sprintf("SELECT u.id, u.email, u.fname, u.birth FROM %s f JOIN %s u on (f.accepter=u.id or f.requester=u.id ) and  (PERIOD_DIFF(DATE_FORMAT(FROM_UNIXTIME(u.birth), '%%j'), DATE_FORMAT(NOW(), '%%j')) BETWEEN 1 and 14) and f.approve = '1'  WHERE (f.accepter in(SELECT u.id FROM %s  f JOIN %s u on f.accepter=u.id or f.requester=u.id WHERE  f.approve='1' OR (u.birth BETWEEN CURRENT_TIMESTAMP and (CURRENT_TIMESTAMP + 1209600))  AND ( (f.accepter=:id or f.requester=:id)) or f.requester in(SELECT u.id FROM friends  f WHERE f.approve='1' AND (f.accepter=:id or f.requester=:id)))) AND u.id <>:id  GROUP BY u.id  ORDER BY u.birth ASC", Friends::tableName(), User::tableName(), Friends::tableName(), User::tableName());
        $friends = $connection->createCommand($query, ['id'=>$userId])->queryAll();

        $response = array_map(function($value) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($value['birth']);
            $value['birthday'] = $dateTime->format('F j');
            return $value;
        }, $friends);

        return [
            'success' => true,
            'data' => $response
        ];
    }
}
