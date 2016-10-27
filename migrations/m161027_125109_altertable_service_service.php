<?php

use yii\db\Schema;
use yii\db\Migration;

class m161027_125109_altertable_service_service extends Migration
{
    public function up()
    {
    	$this->addColumn('{{%service_service}}','calculator',Schema::TYPE_STRING."(155)");
    	$this->addColumn('{{%service_service}}','settings',Schema::TYPE_TEXT);
    }

    public function down()
    {
        $this->dropColumn('{{%service_service}}', 'calculator');
        $this->dropColumn('{{%service_service}}', 'settings');
    }
}
