<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Enum\Rating;
use Core\Domain\ValueObject\Uuid;

class Video
{
    use MethodsMagicsTrait;

    protected array $categoriesId = [];
    protected array $genresId = [];
    protected array $castMembersId = [];

    public function __construct(
        protected string $title,
        protected string $description,
        protected int    $yearLaunched,
        protected int    $duration,
        protected bool   $opened,
        protected Rating $rating,
        protected ?Uuid  $id = null,
        protected bool   $published = false,
    )
    {
        $this->id ??= Uuid::random();
    }

    public function addCategoryId(string $categoryId)
    {
        $this->categoriesId[] = $categoryId;
    }

    public function removeCategoryId(string $categoryId)
    {
        array_splice($this->categoriesId, array_search($categoryId, $this->categoriesId), 1);
    }

    public function addGenreId(string $genreId)
    {
        $this->genresId[] = $genreId;
    }

    public function removeGenreId(string $genreId)
    {
        array_splice($this->genresId, array_search($genreId, $this->genresId), 1);
    }

    public function addCastMemberId(string $castMemberId)
    {
        $this->castMembersId[] = $castMemberId;
    }

    public function removeCastMemberId(string $castMemberId)
    {
        $this->castMembersId = array_diff($this->castMembersId, [$castMemberId]);
    }

}
