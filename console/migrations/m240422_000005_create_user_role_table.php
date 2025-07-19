<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_role}}` va `{{%role}}`,
 * shuningdek default rollarni insert qiladi.
 */
class m240422_000005_create_user_role_table extends Migration
{
    public function safeUp()
    {
        // 1. role jadvalini yaratamiz
        $this->createTable('{{%role}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // 2. user_role bog‘lovchi jadval
        $this->createTable('{{%user_role}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-user_role-user_id',
            '{{%user_role}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_role-role_id',
            '{{%user_role}}',
            'role_id',
            '{{%role}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Unikal kombinatsiya
        $this->createIndex(
            'idx-user_role-user_id-role_id',
            '{{%user_role}}',
            ['user_id', 'role_id'],
            true
        );

        // 3. Default rollarni qo‘shamiz
        $time = time();
        $this->batchInsert('{{%role}}', ['name', 'created_at', 'updated_at'], [
            ['user', $time, $time],
            ['admin', $time, $time],
            ['creator', $time, $time],
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user_role-user_id', '{{%user_role}}');
        $this->dropForeignKey('fk-user_role-role_id', '{{%user_role}}');

        $this->dropTable('{{%user_role}}');
        $this->dropTable('{{%role}}');
    }
}
