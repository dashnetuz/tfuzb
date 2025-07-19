<?php

use yii\db\Migration;

class m240424_000001_create_permission_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%permission}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->unique(),
            'description' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%permission}}');
    }
}
