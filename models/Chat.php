<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat".
 *
 * @property integer $id
 * @property integer $sender
 * @property integer $receiver
 * @property string $data
 * @property string $datetime
 * @property integer $seen
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender', 'receiver', 'data'], 'required'],
            [['sender', 'receiver', 'seen'], 'integer'],
            [['data'], 'string'],
            [['datetime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender' => 'Sender',
            'receiver' => 'Receiver',
            'data' => 'Data',
            'datetime' => 'Datetime',
            'seen' => 'Seen',
        ];
    }
}
