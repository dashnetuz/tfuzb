<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content_text}}`.
 */
class m240504_000003_create_content_text_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content_text}}', [
            'id' => $this->primaryKey(),
            'part_content_id' => $this->integer()->notNull()->unique(),
            'text_uz' => $this->text(),
            'text_ru' => $this->text(),
            'text_en' => $this->text(),
        ]);

        $this->addForeignKey('fk_text_part_content', '{{%content_text}}', 'part_content_id', '{{%part_content}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_text_part_content', '{{%content_text}}');
        $this->dropTable('{{%content_text}}');
    }
}
