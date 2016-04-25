<?php

namespace app\components;

use yii\db\ActiveRecord;

class Model extends ActiveRecord {

    /**
     * @var \yii\db\Connection $connection
     */
    public $connection;
    public function init(){
        $this->connection = \Yii::$app->db;
    }

    /**
     * @param $query
     * @param array $params
     * @return array
     */
    public function getList($query, $params = [])
    {
        return $this->connection->createCommand($query, $params)->queryAll();
    }
}
