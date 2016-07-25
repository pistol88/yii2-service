<?php

use yii\db\Schema;
use yii\db\Migration;

class m160723_112714_Mass extends Migration {

    public function safeUp() {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        } else {
            $tableOptions = null;
        }
        $connection = Yii::$app->db;

        try {
            $this->createTable('{{%service_category}}', [
                'id' => Schema::TYPE_PK . "",
                'category_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'sort' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                ], $tableOptions);

            $this->createTable('{{%service_price}}', [
                'id' => Schema::TYPE_PK . "",
                'service_type' => Schema::TYPE_STRING . "(255) NOT NULL",
                'service_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'price' => Schema::TYPE_DECIMAL . "(11,2)",
                'category_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'description' => Schema::TYPE_TEXT . "",
                ], $tableOptions);

            $this->createTable('{{%service_service}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'parent_id' => Schema::TYPE_INTEGER . "(11)",
                'sort' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'description' => Schema::TYPE_TEXT . "",
                ], $tableOptions);

            $this->createTable('{{%service_complex}}', [
                'id' => Schema::TYPE_PK . "",
                'sort' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                ], $tableOptions);

            $this->createTable('{{%service_to_complex}}', [
                'id' => Schema::TYPE_PK . "",
                'service_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'complex_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                ], $tableOptions);

            $this->addForeignKey(
                'fk_service', '{{%service_price}}', 'service_id', '{{%service_service}}', 'id', 'CASCADE', 'CASCADE'
            );

            $this->addForeignKey(
                'fk_service', '{{%service_service}}', 'category_id', '{{%service_category}}', 'id', 'CASCADE', 'CASCADE'
            );
            
            $this->addForeignKey(
                'fk_one', '{{%service_to_complex}}', 'category_id', '{{%service_category}}', 'id', 'CASCADE', 'CASCADE'
            );
            
            $this->addForeignKey(
                'fk_two', '{{%service_to_complex}}', 'service_id', '{{%service_service}}', 'id', 'CASCADE', 'CASCADE'
            );
            
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' ';
        }
    }

    public function safeDown() {
        $connection = Yii::$app->db;
        try {
            $this->dropTable('{{%service_category}}');
            $this->dropTable('{{%service_price}}');
            $this->dropTable('{{%service_service}}');
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' ';
        }
    }

}
