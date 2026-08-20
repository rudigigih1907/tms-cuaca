<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cuaca}}`.
 */
class m260819_094449_create_cuaca_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // Menggunakan engine InnoDB dan charset utf8mb4
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cuaca}}', [
            'id' => $this->primaryKey(),
            'kode_adm4' => $this->string(20)->notNull(),
            'local_datetime' => $this->dateTime()->notNull(),
            'analysis_date' => $this->dateTime()->notNull(),
            'suhu' => $this->integer()->defaultValue(null),
            'kelembapan' => $this->integer()->defaultValue(null),
            'kondisi_cuaca' => $this->string(100)->defaultValue(null),
            'kecepatan_angin' => $this->float()->defaultValue(null),
            'arah_angin' => $this->string(50)->defaultValue(null),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Membuat Unique Key untuk kode_adm4 dan local_datetime
        $this->createIndex(
            'uk_adm4_datetime',
            '{{%cuaca}}',
            ['kode_adm4', 'local_datetime'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('uk_adm4_datetime', '{{%cuaca}}');
        $this->dropTable('{{%cuaca}}');
    }
}
