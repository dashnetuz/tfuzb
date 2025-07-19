<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%essay_criteria}}`
 * and adds columns to `{{%quiz_answer}}` for essay grading.
 */
class m240505_000002_create_essay_criteria_table extends Migration
{
    public function safeUp()
    {
        // Jadval: essay_criteria
        $this->createTable('{{%essay_criteria}}', [
            'id' => $this->primaryKey(),
            'quiz_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'weight' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk-essay_criteria-quiz_id',
            '{{%essay_criteria}}',
            'quiz_id',
            '{{%quiz}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Ustunlar: quiz_answer uchun
        $this->addColumn('{{%quiz_answer}}', 'criteria_scores', $this->text()->after('answer_text'));
        $this->addColumn('{{%quiz_answer}}', 'total_score', $this->float()->after('criteria_scores'));
    }

    public function safeDown()
    {
        // FK olib tashlash
        $this->dropForeignKey('fk-essay_criteria-quiz_id', '{{%essay_criteria}}');

        // Jadval o‘chirish
        $this->dropTable('{{%essay_criteria}}');

        // quiz_answer ustunlarini o‘chirish
        $this->dropColumn('{{%quiz_answer}}', 'criteria_scores');
        $this->dropColumn('{{%quiz_answer}}', 'total_score');
        $this->dropColumn('{{%quiz_answer}}', 'feedback');
    }
}
