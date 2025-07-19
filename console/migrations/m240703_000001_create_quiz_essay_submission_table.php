<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quiz_essay_submission}}`.
 */
class m240703_000001_create_quiz_essay_submission_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%quiz_essay_submission}}', [
            'id' => $this->primaryKey(),
            'quiz_id' => $this->integer()->notNull()->comment('Essay Quiz ID'),
            'part_id' => $this->integer()->notNull()->comment('Part ID'),
            'user_id' => $this->integer()->notNull()->comment('Foydalanuvchi ID'),
            'essay_text' => $this->text()->comment('Foydalanuvchining yozgan matni'),
            'is_submitted' => $this->boolean()->defaultValue(false)->comment('Yuborildimi'),
            'is_checked' => $this->boolean()->defaultValue(false)->comment('Baholanganmi'),
            'score' => $this->integer()->comment('Olingan ball'),
            'total' => $this->integer()->defaultValue(100)->comment('Maksimal ball'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Indekslar
        $this->createIndex('idx-quiz_essay_submission-quiz_id', '{{%quiz_essay_submission}}', 'quiz_id');
        $this->createIndex('idx-quiz_essay_submission-part_id', '{{%quiz_essay_submission}}', 'part_id');
        $this->createIndex('idx-quiz_essay_submission-user_id', '{{%quiz_essay_submission}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%quiz_essay_submission}}');
    }
}
