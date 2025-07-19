<?php

use yii\db\Migration;

class m240504_000001_create_content_type_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%content_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique(),
            'label' => $this->string(100),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->batchInsert('{{%content_type}}', ['name', 'label'], [
            ['text', 'Matn'],
            ['picture', 'Rasm'],
            ['video', 'Video'],
            ['pdf', 'PDF fayl'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%content_type}}');
    }
}
