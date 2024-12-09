<?php

namespace Core\UseCase\Video\Builder;

use Core\Domain\Entity\Video as Entity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;

class BuilderVideo implements Builder
{
    private ?Entity $entity = null;

    public function __construct()
    {
        $this->reset();
    }

    private function reset()
    {
        $this->entity = null;
    }

    public function createEntity(object $input): void
    {
        $this->entity = new Entity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
        );

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

    public function addMediaVideo(string $path, MediaStatus $mediaStatus): void
    {
        $media = new Media(
            filePath: $path,
            mediaStatus: $mediaStatus
        );
        $this->entity->setVideoFile($media);
    }

    public function addTrailer(string $path, MediaStatus $mediaStatus): void
    {
        $this->entity->setTrailerFile(new Media(
            filePath: $path,
            mediaStatus: MediaStatus::COMPLETE
        ));
    }

    public function addThumb(string $path): void
    {
        $this->entity->setThumbFile(new Image(
            path: $path,
        ));
    }

    public function addThumbHalf(string $path): void
    {
        $this->entity->setThumbHalf(new Image(
            path: $path,
        ));
    }

    public function addBanner(string $path): void
    {
        $this->entity->setBannerFile(new Image(
            path: $path,
        ));
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
