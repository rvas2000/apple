<?php

namespace backend\models;

use yii\db\ActiveRecord;

class AppleColor extends ActiveRecord
{

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getColorName(): string
    {
        return $this->color_name;
    }

    /**
     * @param string $colorName
     * @return self
     */
    public function setColorName(string $colorName): self
    {
        $this->color_name = $colorName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRgb(): string
    {
        return $this->rgb;
    }

    /**
     * @param string $rgb
     * @return self
     */
    public function setRgb(string $rgb): self
    {
        $this->rgb = $rgb;
        return $this;
    }

    /**
     * Получить случайный цвет из всей палитры
     *
     * @return self
     */
    public static function getRandomColor(): self
    {
        $colors = self::find()->all();
        $cnt = count($colors);

        $i = rand(0, $cnt - 1);

        return $colors[$i];
    }

}