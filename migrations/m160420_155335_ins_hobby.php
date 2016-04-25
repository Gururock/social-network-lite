<?php

use yii\db\Migration;

class m160420_155335_ins_hobby extends Migration
{
    protected $tableName = '{{%hobby}}';

    public function safeUp()
    {
        $this->insert($this->tableName,array(
            'title'=>'Gaming',
        ));
        $this->insert($this->tableName,array(
            'title'=>'Art',
        ));
        $this->insert($this->tableName,array(
            'title'=>'Design',
        ));
        $this->insert($this->tableName,array(
            'title'=>'Architecture',
        ));
        $this->insert($this->tableName,array(
            'title'=>'Development',
        ));
        $this->insert($this->tableName,array(
            'title'=>'Science',
        ));
        $this->insert($this->tableName,array(
            'title'=>'History',
        ));

    }

    public function down()
    {
        echo "m130726_010519_insert_user does not support migration down.\n";
        return false;
    }
}
