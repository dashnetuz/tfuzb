<?php

use yii\db\Migration;

/**
 * Class m240430_000001_create_course_structure
 */
class m240430_000001_create_course_structure extends Migration
{
    public function safeUp()
    {
        // category
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'position' => $this->integer()->defaultValue(0),
            'url_uz' => $this->string()->notNull()->unique(),
            'url_ru' => $this->string()->notNull()->unique(),
            'url_en' => $this->string()->notNull()->unique(),
            'picture' => $this->string(),
            'title_uz' => $this->string()->notNull(),
            'title_ru' => $this->string()->notNull(),
            'title_en' => $this->string()->notNull(),
            'description_uz' => $this->text(),
            'description_ru' => $this->text(),
            'description_en' => $this->text(),
            'is_active' => $this->boolean()->defaultValue(false),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // course
        $this->createTable('{{%course}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'position' => $this->integer()->defaultValue(0),
            'url_uz' => $this->string()->notNull()->unique(),
            'url_ru' => $this->string()->notNull()->unique(),
            'url_en' => $this->string()->notNull()->unique(),
            'picture' => $this->string(),
            'title_uz' => $this->string()->notNull(),
            'title_ru' => $this->string()->notNull(),
            'title_en' => $this->string()->notNull(),
            'description_uz' => $this->text(),
            'description_ru' => $this->text(),
            'description_en' => $this->text(),
            'is_active' => $this->boolean()->defaultValue(false),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk-course-category_id', '{{%course}}', 'category_id', '{{%category}}', 'id', 'CASCADE');

        // lesson
        $this->createTable('{{%lesson}}', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer()->notNull(),
            'position' => $this->integer()->defaultValue(0),
            'url_uz' => $this->string()->notNull()->unique(),
            'url_ru' => $this->string()->notNull()->unique(),
            'url_en' => $this->string()->notNull()->unique(),
            'picture' => $this->string(),
            'title_uz' => $this->string()->notNull(),
            'title_ru' => $this->string()->notNull(),
            'title_en' => $this->string()->notNull(),
            'description_uz' => $this->text(),
            'description_ru' => $this->text(),
            'description_en' => $this->text(),
            'is_active' => $this->boolean()->defaultValue(false),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk-lesson-course_id', '{{%lesson}}', 'course_id', '{{%course}}', 'id', 'CASCADE');

        // part
        $this->createTable('{{%part}}', [
            'id' => $this->primaryKey(),
            'lesson_id' => $this->integer()->notNull(),
            'position' => $this->integer()->defaultValue(0),
            'url_uz' => $this->string()->notNull()->unique(),
            'url_ru' => $this->string()->notNull()->unique(),
            'url_en' => $this->string()->notNull()->unique(),
            'title_uz' => $this->string()->notNull(),
            'title_ru' => $this->string()->notNull(),
            'title_en' => $this->string()->notNull(),
            'description_uz' => $this->text(),
            'description_ru' => $this->text(),
            'description_en' => $this->text(),
            'is_active' => $this->boolean()->defaultValue(false),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk-part-lesson_id', '{{%part}}', 'lesson_id', '{{%lesson}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-part-lesson_id', '{{%part}}');
        $this->dropTable('{{%part}}');

        $this->dropForeignKey('fk-lesson-course_id', '{{%lesson}}');
        $this->dropTable('{{%lesson}}');

        $this->dropForeignKey('fk-course-category_id', '{{%course}}');
        $this->dropTable('{{%course}}');

        $this->dropTable('{{%category}}');
    }
}
