<?php

namespace app\models;

use Yii;
use \app\components\Model;
/**
 * This is the model class for table "{{%hobby}}".
 *
 * @property integer $id
 * @property string $title
 */
class Hobby extends Model
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hobby}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }
    
    public function getList(){
        $query = "SELECT * FROM ".self::tableName();
        return parent::getList($query);
    }
}
