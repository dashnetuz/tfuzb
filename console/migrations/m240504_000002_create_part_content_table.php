<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%part_content}}`.
 */
class m240504_000002_create_part_content_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%part_content}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'position' => $this->integer()->defaultValue(0),
            'user_id' => $this->integer(),
            'status' => $this->smallInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_part_content_part', '{{%part_content}}', 'part_id', '{{%part}}', 'id', 'CASCADE');
        $this->addForeignKey('fk_part_content_type', '{{%part_content}}', 'type_id', '{{%content_type}}', 'id', 'RESTRICT');
        $this->addForeignKey('fk_part_content_user', '{{%part_content}}', 'user_id', '{{%user}}', 'id', 'SET NULL');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_part_content_part', '{{%part_content}}');
        $this->dropForeignKey('fk_part_content_type', '{{%part_content}}');
        $this->dropForeignKey('fk_part_content_user', '{{%part_content}}');
        $this->dropTable('{{%part_content}}');
    }
}
