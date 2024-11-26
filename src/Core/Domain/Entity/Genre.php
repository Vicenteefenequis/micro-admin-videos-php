<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre
{

    use MethodsMagicsTrait;

    public function __construct(
        protected string $name,
        protected ?Uuid  $id = null,
        protected bool $isActive = true,
        protected ?DateTime $createdAt = null,
    )
    {
        $this->id ??= Uuid::random();
        $this->createdAt ??= new DateTime();
    }

    public function deactivate()
    {
        $this->isActive = false;
    }

    public function activate()
    {
        $this->isActive = true;
    }

    public function update(string $name)
    {
        $this->name = $name;
    }

}