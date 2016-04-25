<?php

use yii\db\Migration;

class m160420_192251_edit_friends extends Migration
{
    public function up()
    {
        $this->renameColumn('friends', 'user1', 'requester');
        $this->renameColumn('friends', 'user2', 'accepter');
        
    }

    public function down()
    {
        echo "m160420_192251_edit_friends cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
