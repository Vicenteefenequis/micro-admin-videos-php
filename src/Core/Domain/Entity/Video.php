<?php

namespace Core\Domain\Entity;

use Core\Domain\Enum\Rating;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Notification\NotificationException;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Video extends Entity
{

    protected array $categoriesId = [];
    protected array $genresId = [];
    protected array $castMembersId = [];

    public function __construct(
        protected string    $title,
        protected string    $description,
        protected int       $yearLaunched,
        protected int       $duration,
        protected bool      $opened,
        protected Rating    $rating,
        protected ?Uuid     $id = null,
        protected bool      $published = false,
        protected ?DateTime $createdAt = null,
        protected ?Image    $thumbFile = null,
        protected ?Image    $thumbHalf = null,
        protected ?Image    $bannerFile = null,
        protected ?Media    $trailerFile = null,
        protected ?Media    $videoFile = null,
    )
    {
        parent::__construct();
        $this->id ??= Uuid::random();
        $this->createdAt ??= new DateTime();
        $this->validation();
    }

    public
    function addCategoryId(string $categoryId)
    {
        $this->categoriesId[] = $categoryId;
    }

    public
    function removeCategoryId(string $categoryId)
    {
        array_splice($this->categoriesId, array_search($categoryId, $this->categoriesId), 1);
    }

    public
    function addGenreId(string $genreId)
    {
        $this->genresId[] = $genreId;
    }

    public
    function removeGenreId(string $genreId)
    {
        array_splice($this->genresId, array_search($genreId, $this->genresId), 1);
    }

    public
    function addCastMemberId(string $castMemberId)
    {
        $this->castMembersId[] = $castMemberId;
    }

    public
    function removeCastMemberId(string $castMemberId)
    {
        $this->castMembersId = array_diff($this->castMembersId, [$castMemberId]);
    }

    public
    function thumbFile(): ?Image
    {
        return $this->thumbFile;
    }

    public
    function thumbHalf(): ?Image
    {
        return $this->thumbHalf;
    }

    public
    function bannerFile(): ?Image
    {
        return $this->bannerFile;
    }

    public
    function trailerFile(): ?Media
    {
        return $this->trailerFile;
    }

    public function videoFile(): ?Media
    {
        return $this->videoFile;
    }

    protected function validation()
    {
        if (empty($this->title))
            $this->notification->addError([
                'context' => 'video',
                'message' => 'Should not be empty or null'
            ]);

        if (strlen($this->title) < 3)
            $this->notification->addError([
                'context' => 'video',
                'message' => 'Invalid quantity'
            ]);

        if (strlen($this->description) < 3)
            $this->notification->addError([
                'context' => 'video',
                'message' => 'Invalid quantity'
            ]);

        if ($this->notification->hasErrors()) {
            throw new NotificationException($this->notification->messages('video'));
        }
    }

}
