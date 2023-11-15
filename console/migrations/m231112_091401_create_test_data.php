<?php

use yii\db\Migration;

/**
 * Class m231112_091401_create_test_data
 */
class m231112_091401_create_test_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('apple',[
            'status_id' => 1,
            'color_id' => 1,
            'set_date' => '2023-11-11 13:51:00',
        ]);

        $this->insert('apple',[
            'status_id' => 1,
            'color_id' => 2,
            'set_date' => '2023-11-11 17:46:00',
        ]);

        $this->insert('apple',[
            'status_id' => 1,
            'color_id' => 3,
            'set_date' => '2023-11-12 10:31:00',
        ]);

        $this->insert('apple',[
            'status_id' => 1,
            'color_id' => 1,
            'set_date' => '2023-11-11 12:20:00',
        ]);

        $this->insert('apple',[
            'status_id' => 2,
            'color_id' => 2,
            'set_date' => '2023-12-11 10:25:00',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('apple');
        return true;
    }

}
