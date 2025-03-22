<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%profile}}`.
 */
class m250224_174151_create_profile_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'first_name' => $this->string()->null(),
            'last_name' => $this->string()->null(),
            'middle_name' => $this->string()->null(),
            'tell' => $this->string()->null(),
            'birth_date' => $this->date()->null(),
        ]);

        // `user_id` bilan bog‘langan chet el kaliti
        $this->addForeignKey(
            'fk-profile-user_id',
            '{{%profile}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-profile-user_id', '{{%profile}}');
        $this->dropTable('{{%profile}}');
    }
}
