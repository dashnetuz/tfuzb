<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_part_progress}}`.
 */
class m240703_000001_create_user_part_progress_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user_part_progress}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'part_id' => $this->integer()->notNull(),
            'is_completed' => $this->boolean()->defaultValue(false),
            'completed_at' => $this->dateTime()->null(),
        ]);

        $this->createIndex(
            'idx-user_part_progress-user_id',
            '{{%user_part_progress}}',
            'user_id'
        );

        $this->createIndex(
            'idx-user_part_progress-part_id',
            '{{%user_part_progress}}',
            'part_id'
        );

        $this->addForeignKey(
            'fk-user_part_progress-user_id',
            '{{%user_part_progress}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_part_progress-part_id',
            '{{%user_part_progress}}',
            'part_id',
            '{{%part}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-unique-user-part',
            '{{%user_part_progress}}',
            ['user_id', 'part_id'],
            true // unique
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user_part_progress-user_id', '{{%user_part_progress}}');
        $this->dropForeignKey('fk-user_part_progress-part_id', '{{%user_part_progress}}');

        $this->dropIndex('idx-user_part_progress-user_id', '{{%user_part_progress}}');
        $this->dropIndex('idx-user_part_progress-part_id', '{{%user_part_progress}}');
        $this->dropIndex('idx-unique-user-part', '{{%user_part_progress}}');

        $this->dropTable('{{%user_part_progress}}');
    }
}
