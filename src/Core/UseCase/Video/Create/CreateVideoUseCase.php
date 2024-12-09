<?php

namespace Core\UseCase\Video\Create;

use Core\Domain\Entity\Video as Entity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Events\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\Video\Builder\BuilderVideo;
use Core\UseCase\Video\Create\DTO\{
    CreateInputVideoDTO,
    CreateOutputVideoDTO
};
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Throwable;

class CreateVideoUseCase
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

    public function execute(CreateInputVideoDTO $input): CreateOutputVideoDTO
    {
        $this->validateAllIds($input);
        $this->builder->createEntity($input);

        try {
            $this->repository->insert($this->builder->getEntity());

            $this->storageFiles($input);

            $this->repository->updateMedia($this->builder->getEntity());


            // storage media, using $id of entity persist

            // $eventManager to dispatch event

            $this->transaction->commit();
            return $this->output();
        } catch (Throwable $th) {
            $this->transaction->rollback();

            // (isset($pathMedia)) $this->storage->delete($pathMedia);
            throw $th;
        }

    }


    private function storageFiles(object $input): void
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

    private function storageFile(string $path, ?array $media = null): ?string
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

    private function validateIds(array $ids, $repository, string $singularLabel, ?string $pluralLabel = null)
    {
        $idsDb = $repository->getIdsListIds($ids);

        $arrayDiff = array_diff($ids, $idsDb);

        if (count($arrayDiff)) {
            $msg = sprintf('%s %s not found', count($arrayDiff) > 1 ? $pluralLabel ?? $singularLabel . 's' : $singularLabel, implode(', ', $arrayDiff));

            throw new NotFoundException($msg);
        }
    }


    private function output(): CreateOutputVideoDTO
    {
        $entity = $this->builder->getEntity();
        return new CreateOutputVideoDTO(
            id: $entity->id(),
            title: $entity->title,
            description: $entity->description,
            yearLaunched: $entity->yearLaunched,
            duration: $entity->duration,
            opened: $entity->opened,
            rating: $entity->rating,
            categories: $entity->categoriesId,
            genres: $entity->genresId,
            castMembers: $entity->castMembersId,
            videoFile: $entity->videoFile()?->filePath,
            trailerFile: $entity->trailerFile()?->filePath,
            thumbFile: $entity->thumbFile()?->path(),
            thumbHalf: $entity->thumbHalf()?->path(),
            bannerFile: $entity->bannerFile()?->path(),
        );
    }

}
