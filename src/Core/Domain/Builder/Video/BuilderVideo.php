<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\Video as Entity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;

class BuilderVideo implements Builder
{
    protected ?Entity $entity = null;

    public function __construct()
    {
        $this->reset();
    }

    private function reset()
    {
        $this->entity = null;
    }

    public function createEntity(object $input): Builder
    {
        $this->entity = new Entity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
        );
        $this->addIds($input);

        return $this;
    }

    protected function addIds(object $input)
    {
        foreach ($input->categories as $categoryId) {
            $this->entity->addCategoryId($categoryId);
        }
        foreach ($input->genres as $genreId) {
            $this->entity->addGenreId($genreId);
        }
        foreach ($input->castMembers as $castMemberId) {
            $this->entity->addCastMemberId($castMemberId);
        }
    }

    public function addMediaVideo(string $path, MediaStatus $mediaStatus): Builder
    {
        $media = new Media(
            filePath: $path,
            mediaStatus: $mediaStatus
        );
        $this->entity->setVideoFile($media);
        return $this;
    }

    public function addTrailer(string $path, MediaStatus $mediaStatus): Builder
    {
        $this->entity->setTrailerFile(new Media(
            filePath: $path,
            mediaStatus: MediaStatus::COMPLETE
        ));
        return $this;
    }

    public function addThumb(string $path): Builder
    {
        $this->entity->setThumbFile(new Image(
            path: $path,
        ));
        return $this;
    }

    public function addThumbHalf(string $path): Builder
    {
        $this->entity->setThumbHalf(new Image(
            path: $path,
        ));
        return $this;
    }

    public function addBanner(string $path): Builder
    {
        $this->entity->setBannerFile(new Image(
            path: $path,
        ));
        return $this;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
