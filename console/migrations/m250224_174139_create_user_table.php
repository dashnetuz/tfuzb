<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m250224_174139_create_user_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if (!$this->db->schema->getTableSchema('{{%user}}', true)) {
            $this->createTable('{{%user}}', [
                'id' => $this->primaryKey(),
                'username' => $this->string()->notNull()->unique(),
                'email' => $this->string()->notNull()->unique(), // 📌 Email user jadvalida qoldi
                'auth_key' => $this->string(32)->notNull(),
                'password_hash' => $this->string()->null(),
                'password_reset_token' => $this->string()->unique()->null(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'auth_type' => $this->string()->notNull()->defaultValue('local'),
                'auth_provider_id' => $this->string()->null(),
                'verification_token' => $this->string()->defaultValue(null),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
            ], $tableOptions);

            $this->createIndex(
                'idx-user-auth_provider_id-auth_type',
                '{{%user}}',
                ['auth_provider_id', 'auth_type'],
                true
            );
        } else {
            echo "⚠️ Table 'user' already exists. Skipping migration.\n";
        }
    }

    public function safeDown()
    {
        $this->dropIndex('idx-user-auth_provider_id-auth_type', '{{%user}}');
        $this->dropTable('{{%user}}');
    }
}
