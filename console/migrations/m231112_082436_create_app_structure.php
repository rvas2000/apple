<?php

use yii\db\Migration;

/**
 * Class m231112_082436_create_app_structure
 */
class m231112_082436_create_app_structure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Справочник статусов
        $this->createTable(
            'apple_status',
            [
                'id' => $this->primaryKey(),
                'status_name' => $this->string(50)->notNull(),
            ],
            $tableOptions
        );

        // Справочник цветов
        $this->createTable(
            'apple_color',
            [
                'id' => $this->primaryKey(),
                'color_name' => $this->string(50)->notNull(),
                'rgb' => $this->string(10)->notNull()->comment('RGB-представление цвета')
            ],
            $tableOptions
        );

        // Все яблоки
        $this->createTable(
            'apple',
            [
                'id' => $this->primaryKey(),
                'status_id' => $this->integer()->notNull()->comment('ID статуса'),
                'color_id' => $this->integer()->notNull()->comment('ID цвета'),
                'set_date' => $this->timestamp()->notNull()->comment('Дата появления'),
                'fail_date' => $this->timestamp()->defaultValue(null)->comment('Дата падения'),
                'eaten_percent' => $this->integer()->notNull()->defaultValue(0)->comment('Процент съеденного'),
                'deleted' => $this->boolean()->notNull()->defaultValue(false)->comment('Удалено'),
                'del_date' => $this->timestamp()->defaultValue(null)->comment('Дата удаления'),
            ],
            $tableOptions
        );

        $this->addForeignKey('fk__apple__apple_status', 'apple', 'status_id', 'apple_status', 'id');
        $this->addForeignKey('fk__apple__apple_color', 'apple', 'color_id', 'apple_color', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk__apple__apple_color', 'apple');
        $this->dropForeignKey('fk__apple__apple_status', 'apple');
        $this->dropTable('apple_color');
        $this->dropTable('apple_status');
        $this->dropTable('apple');

        return true;
    }
}
