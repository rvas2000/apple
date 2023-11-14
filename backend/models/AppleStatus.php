<?php

namespace backend\models;

use yii\db\ActiveRecord;

class AppleStatus extends ActiveRecord
{
    protected ?int $id;
    protected string $statusName;

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
    public function getStatusName(): string
    {
        return $this->status_name;
    }

    /**
     * @param string $statusName
     * @return self
     */
    public function setStatusName(string $statusName): self
    {
        $this->status_name = $statusName;
        return $this;
    }

}