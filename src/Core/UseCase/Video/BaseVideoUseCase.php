<?php

namespace Core\UseCase\Video;

use Core\Domain\Repository\{
    CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface
};
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Events\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\Interfaces\{
    FileStorageInterface,
    TransactionInterface
};
use Core\UseCase\Video\Builder\BuilderVideo;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;

abstract class BaseVideoUseCase
{
    protected BuilderVideo $builder;

    public function __construct(
        protected VideoRepositoryInterface      $repository,
        protected TransactionInterface          $transaction,
        protected FileStorageInterface          $storage,
        protected VideoEventManagerInterface    $eventManager,
        protected CategoryRepositoryInterface   $repositoryCategory,
        protected GenreRepositoryInterface      $repositoryGenre,
        protected CastMemberRepositoryInterface $repositoryCastMember,
    )
    {
        $this->builder = new BuilderVideo();
    }

    protected function storageFiles(object $input): void
    {
        $path = $this->builder->getEntity()->id();
        if ($pathVideoFile = $this->storageFile($path, $input->videoFile)) {
            $this->builder->addMediaVideo($pathVideoFile, MediaStatus::PROCESSING);
            $this->eventManager->dispatch(new VideoCreatedEvent($this->builder->getEntity()));
        }

        if ($pathTrailerFile = $this->storageFile($path, $input->trailerFile)) {
            $this->builder->addTrailer(
                path: $pathTrailerFile,
                mediaStatus: MediaStatus::PROCESSING
            );
        }

        if ($pathThumbFile = $this->storageFile($path, $input->thumbFile)) {
            $this->builder->addThumb(
                path: $pathThumbFile,
            );
        }

        if ($pathThumbHalfFile = $this->storageFile($path, $input->thumbHalf)) {
            $this->builder->addThumbHalf(
                path: $pathThumbHalfFile,
            );
        }

        if ($pathBannerFile = $this->storageFile($path, $input->bannerFile)) {
            $this->builder->addBanner(
                path: $pathBannerFile,
            );
        }
    }

    protected function storageFile(string $path, ?array $media = null): ?string
    {
        if ($media) {
            return $this->storage->store(
                path: $path,
                file: $media
            );
        }

        return null;

    }

    protected function validateAllIds(object $input)
    {
        $this->validateIds($input->categories, $this->repositoryCategory, 'Category');
        $this->validateIds($input->castMembers, $this->repositoryCastMember, 'CastMember');
        $this->validateIds($input->genres, $this->repositoryGenre, 'Genre');
    }

    protected function validateIds(array $ids, $repository, string $singularLabel, ?string $pluralLabel = null)
    {
        $idsDb = $repository->getIdsListIds($ids);

        $arrayDiff = array_diff($ids, $idsDb);

        if (count($arrayDiff)) {
            $msg = sprintf('%s %s not found', count($arrayDiff) > 1 ? $pluralLabel ?? $singularLabel . 's' : $singularLabel, implode(', ', $arrayDiff));

            throw new NotFoundException($msg);
        }
    }
}
