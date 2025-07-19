<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content_pdf}}`.
 */
class m240504_000006_create_content_pdf_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content_pdf}}', [
            'id' => $this->primaryKey(),
            'part_content_id' => $this->integer()->notNull()->unique(),
            'file_path' => $this->string()->notNull(),
            'title_uz' => $this->string(),
            'title_ru' => $this->string(),
            'title_en' => $this->string(),
            'description_uz' => $this->text(),
            'description_ru' => $this->text(),
            'description_en' => $this->text(),
        ]);

        $this->addForeignKey('fk_pdf_part_content', '{{%content_pdf}}', 'part_content_id', '{{%part_content}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_pdf_part_content', '{{%content_pdf}}');
        $this->dropTable('{{%content_pdf}}');
    }
}
