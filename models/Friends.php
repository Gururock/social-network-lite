<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "friends".
 *
 * @property integer $id
 * @property string $requester
 * @property string $accepter
 * @property integer $approve
 */
class Friends extends \app\components\Model {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'friends';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['requester', 'accepter'], 'required'],
            [['requester', 'accepter', 'approve'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'requester' => 'Requester',
            'accepter' => 'Accepter',
            'approve' => 'Approve',
        ];
    }

    public function getList($id)
    {
        return parent::getList(sprintf('SELECT requester,accepter FROM %s WHERE requester = :id', self::tableName()), ['id' => $id]);
    }
    
    public function getFriendsFriends($id){
        $model = new User();
        $friends = $this->getList($id);
        $result['owner']['name'] = $model->getFname($id)['fname'];
        foreach($friends as $friends_id){
            $result['owner']['friends'][] = User::getFname($friends_id['accepter']);
            $fof = $this->getList($friends_id['accepter']);
            foreach($fof as $item){
                $label = User::getFname($item['requester']);
                $result['owner']['friends of '.$label['fname']][] = User::getFname($item['accepter']);
            }
        }
        return $result;
    }
}
