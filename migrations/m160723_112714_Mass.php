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
				'parent_id' => Schema::TYPE_INTEGER . "(11)",
                'sort' => Schema::TYPE_INTEGER . "(11)",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                ], $tableOptions);
            $this->createTable('{{%service_property}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(55) NOT NULL",
                'category_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'client_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'status' => Schema::TYPE_STRING . "(55)",
                'comment' => Schema::TYPE_STRING . "(255)",
                'created_at' => Schema::TYPE_DATETIME,
                'updated_at' => Schema::TYPE_DATETIME,
                ], $tableOptions);
            
            $this->createTable('{{%service_price}}', [
                'id' => Schema::TYPE_PK . "",
				'service_type' => Schema::TYPE_STRING . "(255)",
                'sum' => Schema::TYPE_INTEGER . "(11)",
				'price' => Schema::TYPE_DECIMAL . "(11,2)",
                'date' => Schema::TYPE_DATE,
                'user_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
				'service_id' => Schema::TYPE_INTEGER . "(11)",
                'session_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
				'category_id' => Schema::TYPE_INTEGER . "(11)",
				'description' => Schema::TYPE_STRING . "(255)",
                ], $tableOptions);
                
            $this->createTable('{{%service_cost}}', [
                'id' => Schema::TYPE_PK . "",
				'name' => Schema::TYPE_STRING . "(255)",
                'service_type' => Schema::TYPE_STRING . "(255) NOT NULL",
                'service_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'price' => Schema::TYPE_DECIMAL . "(11,2)",
				'sum' => Schema::TYPE_DECIMAL . "(11,2)",
				'date' => Schema::TYPE_DATE,
                'category_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'description' => Schema::TYPE_TEXT . "",
				'session_id' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);
            
            $this->createTable('{{%service_payment}}', [
                'id' => Schema::TYPE_PK . "",
                'type' => "ENUM('in', 'out') NULL",
                'order_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'user_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'worker_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'client_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'session_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'date' => Schema::TYPE_DATE,
                'date_timestamp' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'sum' => Schema::TYPE_DECIMAL . "(11,2)",
                ], $tableOptions);
                
            $this->createTable('{{%service_service}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'parent_id' => Schema::TYPE_INTEGER . "(11)",
                'sort' => Schema::TYPE_INTEGER . "(11)",
                'description' => Schema::TYPE_TEXT . "",
                ], $tableOptions);
                
            $this->createTable('{{%service_custom_service}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'price' => Schema::TYPE_DECIMAL . "(11,2)",
                'date' => Schema::TYPE_DATE . " NOT NULL",
                'user_id' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);
            
            $this->createTable('{{%service_complex}}', [
                'id' => Schema::TYPE_PK . "",
                'sort' => Schema::TYPE_INTEGER . "(11)",
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
                'fk_one', '{{%service_to_complex}}', 'complex_id', '{{%service_complex}}', 'id', 'CASCADE', 'CASCADE'
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
            $this->dropTable('{{%service_complex}}');
            $this->dropTable('{{%service_to_complex}}');
            $this->dropTable('{{%service_cost}}');
            
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' ';
        }
    }
}