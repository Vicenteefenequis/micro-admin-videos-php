<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre
{

    use MethodsMagicsTrait;

    public function __construct(
        protected string $name,
        protected ?Uuid  $id = null,
        protected bool $isActive = true,
        protected array $categoriesId = [],
        protected ?DateTime $createdAt = null,
    )
    {
        $this->id ??= Uuid::random();
        $this->createdAt ??= new DateTime();

        $this->validate();
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
        $this->validate();
    }

    public function addCategory(string $categoryId)
    {
        $this->categoriesId[] = $categoryId;
    }

    public function removeCategory(string $categoryId)
    {
        $this->categoriesId = array_diff($this->categoriesId, [$categoryId]);
    }

    protected function validate()
    {
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
    }

}
