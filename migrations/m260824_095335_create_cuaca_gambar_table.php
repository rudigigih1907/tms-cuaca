<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cuaca_gambar}}`.
 */
class m260824_095335_create_cuaca_gambar_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cuaca_gambar}}', [
            'id' => $this->primaryKey(),
            'cuaca_id' => $this->integer()->notNull(),
            'file_name' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Membuat indeks untuk kolom cuaca_id
        $this->createIndex(
            '{{%idx-cuaca_gambar-cuaca_id}}',
            '{{%cuaca_gambar}}',
            'cuaca_id'
        );

        // Menambahkan foreign key ke tabel cuaca
        $this->addForeignKey(
            '{{%fk-cuaca_gambar-cuaca_id}}',
            '{{%cuaca_gambar}}',
            'cuaca_id',
            '{{%cuaca}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Menghapus foreign key terlebih dahulu
        $this->dropForeignKey(
            '{{%fk-cuaca_gambar-cuaca_id}}',
            '{{%cuaca_gambar}}'
        );

        // Menghapus indeks
        $this->dropIndex(
            '{{%idx-cuaca_gambar-cuaca_id}}',
            '{{%cuaca_gambar}}'
        );

        $this->dropTable('{{%cuaca_gambar}}');
    }
}
