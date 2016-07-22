<?php

use yii\db\Schema;
use yii\db\Migration;

class m160705_112015_Mass extends Migration {

    public function safeUp() {
        $tableOptions = 'ENGINE=InnoDB';
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $this->createTable('{{%autocatalog_category}}', [
                'id' => Schema::TYPE_PK . "",
                'parent_id' => Schema::TYPE_INTEGER . "(11)",
                'name' => Schema::TYPE_STRING . "(55) NOT NULL",
                'text' => Schema::TYPE_TEXT . "",
                'image' => Schema::TYPE_TEXT . "",
                'sort' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);

            $this->createIndex('id', '{{%autocatalog_category}}', 'id,parent_id', 0);
            $this->createTable('{{%autocatalog_mark}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'image' => Schema::TYPE_TEXT . "",
                'text' => Schema::TYPE_TEXT . "",
                ], $tableOptions);

            $this->createTable('{{%autocatalog_model}}', [
                'id' => Schema::TYPE_PK . "",
                'code' => Schema::TYPE_STRING . "(55)",
                'category_id' => Schema::TYPE_INTEGER . "(10) NOT NULL",
                'mark_id' => Schema::TYPE_INTEGER . "(11)",
                'name' => Schema::TYPE_STRING . "(200) NOT NULL",
                'text' => Schema::TYPE_TEXT . " NOT NULL",
                'images' => Schema::TYPE_TEXT . "",
                'sort' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);

            $this->createIndex('category_id', '{{%autocatalog_model}}', 'category_id', 0);
            $this->createIndex('mark_id', '{{%autocatalog_model}}', 'mark_id', 0);
            $this->createTable('{{%autocatalog_model_to_category}}', [
                'id' => Schema::TYPE_PK . "",
                'model_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'category_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                ], $tableOptions);

            $transaction->commit();
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' and rollBack this';
            $transaction->rollBack();
        }
    }

    public function safeDown() {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $this->dropTable('{{%autocatalog_category}}');
            $this->dropTable('{{%autocatalog_mark}}');
            $this->dropTable('{{%autocatalog_model}}');
            $this->dropTable('{{%autocatalog_model_to_category}}');
            $transaction->commit();
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' and rollBack this';
            $transaction->rollBack();
        }
    }

}
