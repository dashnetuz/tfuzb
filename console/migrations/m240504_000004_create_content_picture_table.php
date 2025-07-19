<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content_picture}}`.
 */
class m240504_000004_create_content_picture_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content_picture}}', [
            'id' => $this->primaryKey(),
            'part_content_id' => $this->integer()->notNull()->unique(),
            'file_path' => $this->string()->notNull(),
            'alt' => $this->string(),
            'caption' => $this->string(),
        ]);

        $this->addForeignKey('fk_picture_part_content', '{{%content_picture}}', 'part_content_id', '{{%part_content}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_picture_part_content', '{{%content_picture}}');
        $this->dropTable('{{%content_picture}}');
    }
}
