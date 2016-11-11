<?php

use yii\db\Migration;

class m161110_050318_create_organization_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%service_service}}', 'organization_id', $this->integer());
        $this->addColumn('{{%service_category}}', 'organization_id', $this->integer());
        $this->addColumn('{{%service_complex}}', 'organization_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%service_service}}', 'organization_id');
        $this->dropColumn('{{%service_category}}', 'organization_id');
        $this->dropColumn('{{%service_complex}}', 'organization_id');
        
        return true;
    }
}
