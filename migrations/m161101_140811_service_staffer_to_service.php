<?php

use yii\db\Schema;
use yii\db\Migration;

class m161101_140811_service_staffer_to_service extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%service_staffer_to_service}}',
            [
                'id'=> Schema::TYPE_PK."",
                'staffer_model'=> Schema::TYPE_STRING."(255)",
                'staffer_id'=> Schema::TYPE_INTEGER."(11) NOT NULL",
                'service_model'=> Schema::TYPE_STRING."(255)",
                'service_id'=> Schema::TYPE_INTEGER."(11) NOT NULL",
                'session_id'=> Schema::TYPE_INTEGER."(11)",
                'datetime'=> Schema::TYPE_DATETIME." NOT NULL",
                ],
            $tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%service_staffer_to_service}}');
    }
}
