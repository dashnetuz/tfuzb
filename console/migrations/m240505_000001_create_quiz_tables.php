<?php

use yii\db\Migration;

/**
 * Class m240505_000001_create_quiz_tables
 */
class m240505_000001_create_quiz_tables extends Migration
{
    public function safeUp()
    {
        // quiz
        $this->createTable('{{%quiz}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'type' => $this->tinyInteger()->notNull()->comment('1 = test, 2 = essay'),
            'time_limit' => $this->integer()->notNull()->defaultValue(0)->comment('in minutes'),
            'pass_percent' => $this->integer()->notNull()->defaultValue(60),
            'max_attempt' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk-quiz-part_id', '{{%quiz}}', 'part_id', '{{%part}}', 'id', 'CASCADE');

        // quiz_question
        $this->createTable('{{%quiz_question}}', [
            'id' => $this->primaryKey(),
            'quiz_id' => $this->integer()->notNull(),
            'body' => $this->text()->notNull(),
            'explanation' => $this->text(),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk-question-quiz_id', '{{%quiz_question}}', 'quiz_id', '{{%quiz}}', 'id', 'CASCADE');

        // quiz_option
        $this->createTable('{{%quiz_option}}', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'body' => $this->string()->notNull(),
            'is_correct' => $this->boolean()->notNull()->defaultValue(false),
        ]);

        $this->addForeignKey('fk-option-question_id', '{{%quiz_option}}', 'question_id', '{{%quiz_question}}', 'id', 'CASCADE');

        // quiz_attempt
        $this->createTable('{{%quiz_attempt}}', [
            'id' => $this->primaryKey(),
            'quiz_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'started_at' => $this->dateTime()->notNull(),
            'ended_at' => $this->dateTime(),
            'score' => $this->float(),
            'is_passed' => $this->boolean(),
            'try_index' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk-attempt-quiz_id', '{{%quiz_attempt}}', 'quiz_id', '{{%quiz}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-attempt-user_id', '{{%quiz_attempt}}', 'user_id', '{{%user}}', 'id', 'CASCADE');

        // quiz_answer
        $this->createTable('{{%quiz_answer}}', [
            'id' => $this->primaryKey(),
            'attempt_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'option_id' => $this->integer(),
            'answer_text' => $this->text(),
            'is_correct' => $this->boolean(),
            'feedback' => $this->text(),
        ]);

        $this->addForeignKey('fk-answer-attempt_id', '{{%quiz_answer}}', 'attempt_id', '{{%quiz_attempt}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-answer-question_id', '{{%quiz_answer}}', 'question_id', '{{%quiz_question}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-answer-option_id', '{{%quiz_answer}}', 'option_id', '{{%quiz_option}}', 'id', 'SET NULL');
    }

    public function safeDown()
    {
        $this->dropTable('{{%quiz_answer}}');
        $this->dropTable('{{%quiz_attempt}}');
        $this->dropTable('{{%quiz_option}}');
        $this->dropTable('{{%quiz_question}}');
        $this->dropTable('{{%quiz}}');
    }
}
