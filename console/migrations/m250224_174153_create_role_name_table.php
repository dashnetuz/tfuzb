<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%role_name}}`.
 */
class m250224_174153_create_role_name_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%role_name}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%role_name}}');
    }
}