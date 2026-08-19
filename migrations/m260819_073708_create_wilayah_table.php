<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wilayah}}`.
 */
class m260819_073708_create_wilayah_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wilayah}}', [
            'kode' => $this->string(13)->notNull(),
            'nama' => $this->string(100)->defaultValue(null),
            'PRIMARY KEY(kode)',
        ]);

        // Membuat indeks untuk kolom nama
        $this->createIndex(
            'idx-wilayah-nama',
            '{{%wilayah}}',
            'nama'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-wilayah-nama', '{{%wilayah}}');
        $this->dropTable('{{%wilayah}}');
    }
}
