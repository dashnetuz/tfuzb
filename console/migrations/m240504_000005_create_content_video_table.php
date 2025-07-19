<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content_video}}`.
 */
class m240504_000005_create_content_video_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content_video}}', [
            'id' => $this->primaryKey(),
            'part_content_id' => $this->integer()->notNull()->unique(),
            'youtube_url' => $this->string()->notNull(),
            'title_uz' => $this->string(),
            'title_ru' => $this->string(),
            'title_en' => $this->string(),
            'description_uz' => $this->text(),
            'description_ru' => $this->text(),
            'description_en' => $this->text(),
        ]);

        $this->addForeignKey('fk_video_part_content', '{{%content_video}}', 'part_content_id', '{{%part_content}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_video_part_content', '{{%content_video}}');
        $this->dropTable('{{%content_video}}');
    }
}
