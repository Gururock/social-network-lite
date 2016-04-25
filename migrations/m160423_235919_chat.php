<?php

use yii\db\Migration;

class m160423_235919_chat extends Migration
{
    protected $tableName = '{{%chat}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
          'id' => $this->primaryKey(),
          'sender' => $this->integer()->notNull(),
          'receiver' => $this->integer()->notNull(),
          'data' => $this->text()->notNull(),
          'datetime' => $this->dateTime()->defaultValue(new yii\db\Expression('CURRENT_TIMESTAMP'))->notNull(),
          'seen' => $this->boolean()->defaultValue(0),
        ], $tableOptions);

    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
