<?php

use yii\db\Migration;

class m161110_050318_create_organisation_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%service_service}}', 'organisation_id', $this->integer());
        $this->addColumn('{{%service_category}}', 'organisation_id', $this->integer());
        $this->addColumn('{{%service_complex}}', 'organisation_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%service_service}}', 'organisation_id');
        $this->dropColumn('{{%service_category}}', 'organisation_id');
        $this->dropColumn('{{%service_complex}}', 'organisation_id');
        
        return true;
    }
}
