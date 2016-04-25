<?php

use yii\db\Migration;

class m160420_154923_add_friends extends Migration
{
    protected $tableName = '{{%friends}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user1' => $this->bigInteger()->notNull(),
            'user2' => $this->bigInteger()->notNull(),
            'approve'=>$this->boolean()->defaultValue(false)
        ], $tableOptions);

    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
