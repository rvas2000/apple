<?php

namespace backend\models;

use yii\db\ActiveRecord;

class Apple extends ActiveRecord
{
    public const STATUS_ON_TREE = 1;
    public const STATUS_ON_GROUND = 2;
    public const STATUS_BAD = 3;

    protected const BAD_INTERVAL = 5; // время (в часах), в течение к-рого упавшее яблоко испортится

    protected AppleColor $color;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return AppleStatus
     */
    public function getStatus(): AppleStatus
    {
        return $this->hasOne('backend\models\AppleStatus', ['id' => 'status_id'])->one();
    }

    /**
     * @param AppleStatus $status
     * @return self
     */
    public function setStatus(AppleStatus $status): self
    {
        $this->status_id = $status->getId();
        return $this;
    }

    /**
     * @return AppleColor
     */
    public function getColor(): AppleColor
    {
        return $this->hasOne('backend\models\AppleColor', ['id' => 'color_id'])->one();
    }

    /**
     * @param AppleColor $color
     * @return self
     */
    public function setColor(AppleColor $color): self
    {
        $this->color_id = $color->getId();
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSetDate(): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->set_date);
    }

    /**
     * @param \DateTime $setDate
     * @return self
     */
    public function setSetDate(\DateTime $setDate): self
    {
        $this->set_date = $setDate->format('Y-m-d H:i:s');
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getFallDate(): ?\DateTime
    {
        return $this->fail_date === null ? null : \DateTime::createFromFormat('Y-m-d H:i:s', $this->fail_date);
    }

    /**
     * @param \DateTime|null $fallDate
     * @return self
     */
    public function setFallDate(?\DateTime $fallDate): self
    {
        $this->fail_date = $fallDate === null ? null : $fallDate->format('Y-m-d H:i:s');
        return $this;
    }

    /**
     * @return int
     */
    public function getEatenPercent(): int
    {
        return $this->eaten_percent;
    }

    /**
     * @param int $eatenPercent
     * @return self
     */
    public function setEatenPercent(int $eatenPercent): self
    {
        $this->eaten_percent = $eatenPercent;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return (bool)$this->deleted;
    }

    /**
     * @param bool $deleted
     * @return self
     */
    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDelDate(): ?\DateTime
    {
        return $this->del_date === null ? null : \DateTime::createFromFormat('Y-m-d H:i:s', $this->del_date);
    }

    /**
     * @param \DateTime|null $delDate
     * @return self
     */
    public function setDelDate(?\DateTime $delDate): self
    {
        $this->del_date = $delDate === null ? null : $delDate->format('Y-m-d H:i:s');
        return $this;
    }


    /**
     * Упасть
     *
     * @return void
     */
    public function fall(): void
    {
        if ($this->deleted) {
            throw new \Exception('Это яблоко уже удалено!');
        }

        if (in_array($this->status_id, [self::STATUS_ON_GROUND, self::STATUS_BAD])) {
            throw new \Exception('Упавшее яблоко еще раз упасть не может!');
        }

        $this->status_id = self::STATUS_ON_GROUND;
        $this->setFallDate(new \DateTime());
        $this->save();

    }

    /**
     * Удалить
     *
     * @return void
     */
    public function delete(): void
    {
        if ($this->deleted) {
            throw new \Exception('Это яблоко уже удалено!');
        }

        $this->setDeleted(true);
        $this->setDelDate(new \DateTime());
        $this->save();

    }

    /**
     * Съесть
     *
     * @param int $percent - процент съеденного
     * @return void
     */
    public function eat(int $percent): void
    {
        if ($this->status_id === self::STATUS_ON_TREE) {
            throw new \Exception('Висящее на дереве яблоко съесть невозможно!');
        }

        if ($this->status_id === self::STATUS_BAD) {
            throw new \Exception('Яблоко испорчено. Есть не рекомендуется!');
        }

        if ($this->deleted) {
            throw new \Exception('Это яблоко уже удалено!');
        }

        if ($percent > 100) {
            $percent = 100;
        }

        $v = 1 - $this->eaten_percent / 100; // оставшаяся часть от целого яблока
        $ve = $v * $percent / 100; // съели за этот раз
        $v = $v - $ve; // осталось после того, как съели

        // Корректируем погрешность вычисления - остаться не может меньше 0
        if ($v < 0) {
            $v = 0;
        }

        // Новое значение процента съеденного яблока
        $this->eaten_percent = (int)round((1 - $v) * 100);

        // Съеденное полностью яблоко удаляем из массива
        if ($this->eaten_percent === 100) {
            $this->setDeleted(true);
            $this->setDelDate(new \DateTime());
        }

        $this->save();
    }

    /**
     * Проверка по времени статуса упавших яблок
     *
     * @return self
     */
    public function check(): self
    {
        $fallDate = $this->getFallDate();
        if ($this->status_id === self::STATUS_ON_GROUND && $fallDate !== null) {
            $currentTimestamp = (new \DateTime())->getTimestamp();
            $inteval = $currentTimestamp - $fallDate->getTimestamp();
            if ($inteval >= self::BAD_INTERVAL * 3600) {
                $this->status_id = self::STATUS_BAD;
                $this->save();
            }

        }

        return $this;
    }

    /**
     * Создать случайное кол-во яблок случайного цвета, висящих на дереве
     *
     * @return int - кол-во созданных яблок
     */
    public static function generate(): int
    {
        $n = rand(1, 10);
        for ($i = 1; $i <= $n; $i++) {
            $apple = new Apple();
            $apple->status_id = self::STATUS_ON_TREE;
            $apple
                ->setSetDate(new \DateTime())
                ->setColor(AppleColor::getRandomColor())
                ->save();
        }

        return $n;

    }

    /**
     * Выбрать все яблоки, не помеченные как удаленные
     *
     * @return array
     */
    public static function getAllNotDeleted(): array
    {
        return self::find()
            ->onCondition(['deleted' => 0])
            ->orderBy(['id' => SORT_DESC])->all();
    }

    /**
     * Выбрать все неудаленные яблоки, лежащие на земле
     *
     * @return array
     */
    public static function getAllOnGround(): array
    {
        return self::find()
            ->onCondition(['deleted' => 0])
            ->andOnCondition(['status_id' => self::STATUS_ON_GROUND])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }
}