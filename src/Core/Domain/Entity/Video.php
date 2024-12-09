<?php

namespace Core\Domain\Entity;

use Core\Domain\Enum\Rating;
use Core\Domain\Factory\VideoValidatorFactory;
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
    function setThumbFile(Image $thumbFile): void
    {
        $this->thumbFile = $thumbFile;
    }

    public
    function thumbHalf(): ?Image
    {
        return $this->thumbHalf;
    }

    public
    function setThumbHalf(Image $thumbHalf): void
    {
        $this->thumbHalf = $thumbHalf;
    }

    public
    function bannerFile(): ?Image
    {
        return $this->bannerFile;
    }

    public
    function setBannerFile(Image $bannerFile): void
    {
        $this->bannerFile = $bannerFile;
    }

    public
    function trailerFile(): ?Media
    {
        return $this->trailerFile;
    }

    public
    function setTrailerFile(Media $trailerFile): void
    {
        $this->trailerFile = $trailerFile;
    }

    public function videoFile(): ?Media
    {
        return $this->videoFile;
    }

    public function setVideoFile(Media $videoFile): void
    {
        $this->videoFile = $videoFile;
    }

    protected function validation()
    {
        VideoValidatorFactory::create()->validate($this);

        if ($this->notification->hasErrors())
            throw new NotificationException($this->notification->messages('video'));
    }

}
