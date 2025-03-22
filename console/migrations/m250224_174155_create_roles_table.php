<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%roles}}`.
 */
class m250224_174155_create_roles_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%roles}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-roles-user_id',
            '{{%roles}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-roles-role_id',
            '{{%roles}}',
            'role_id',
            '{{%role_name}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-roles-user_id', '{{%roles}}');
        $this->dropForeignKey('fk-roles-role_id', '{{%roles}}');
        $this->dropTable('{{%roles}}');
    }
}