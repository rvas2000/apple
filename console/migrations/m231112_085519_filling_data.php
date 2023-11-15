<?php

use yii\db\Migration;

/**
 * Class m231112_085519_filling_data
 */
class m231112_085519_filling_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Статусы
        $this->insert('apple_status', [
            'id' => 1,
            'status_name' => 'На дереве'
        ]);

        $this->insert('apple_status', [
            'id' => 2,
            'status_name' => 'На земле'
        ]);

        $this->insert('apple_status', [
            'id' => 3,
            'status_name' => 'Гнилое'
        ]);

        // Цвета
        $this->insert('apple_color', [
            'id' => 1,
            'color_name' => 'Красное',
            'rgb' => 'ff0000'
        ]);

        $this->insert('apple_color', [
            'id' => 2,
            'color_name' => 'Желтое',
            'rgb' => 'FFEA00FF'
        ]);

        $this->insert('apple_color', [
            'id' => 3,
            'color_name' => 'Зеленое',
            'rgb' => '84FF00FF'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('apple');
        $this->delete('apple_status');
        $this->delete('apple_color');
        return true;
    }

}
