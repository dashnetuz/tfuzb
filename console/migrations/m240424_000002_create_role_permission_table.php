<?php

use yii\db\Migration;

class m240424_000002_create_role_permission_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%role_permission}}', [
            'role_id' => $this->integer()->notNull(),
            'permission_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-role_permission', '{{%role_permission}}', ['role_id', 'permission_id']);

        $this->addForeignKey(
            'fk-role_permission-role_id',
            '{{%role_permission}}',
            'role_id',
            '{{%role}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-role_permission-permission_id',
            '{{%role_permission}}',
            'permission_id',
            '{{%permission}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%role_permission}}');
    }
}
