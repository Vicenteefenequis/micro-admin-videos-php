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
use Core\UseCase\Video\Create\DTO\{
    CreateInputVideoDTO,
    CreateOutputVideoDTO
};
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Throwable;

class CreateVideoUseCase
{
    protected Entity $entity;

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
    }

    public function execute(CreateInputVideoDTO $input): CreateOutputVideoDTO
    {
        $this->entity = $this->createEntity($input);

        try {
            $this->repository->insert($this->entity);

            $this->storageFiles($input);

            $this->repository->updateMedia($this->entity);


            // storage media, using $id of entity persist

            // $eventManager to dispatch event

            $this->transaction->commit();
            return $this->output($this->entity);
        } catch (Throwable $th) {
            $this->transaction->rollback();

            // (isset($pathMedia)) $this->storage->delete($pathMedia);
            throw $th;
        }

    }

    private function createEntity(CreateInputVideoDTO $input): Entity
    {

        $this->validateAllIds($input);

        $entity = new Entity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
        );

        foreach ($input->categories as $categoryId) {
            $entity->addCategoryId($categoryId);
        }
        foreach ($input->genres as $genreId) {
            $entity->addGenreId($genreId);
        }
        foreach ($input->castMembers as $castMemberId) {
            $entity->addCastMemberId($castMemberId);
        }

        return $entity;
    }

    private function storageFiles(object $input): void
    {
        if ($pathVideoFile = $this->storageFile($this->entity->id(), $input->videoFile)) {
            $media = new Media(
                filePath: $pathVideoFile,
                mediaStatus: MediaStatus::PROCESSING
            );
            $this->entity->setVideoFile($media);
            $this->eventManager->dispatch(new VideoCreatedEvent($this->entity));
        }

        if ($pathTrailerFile = $this->storageFile($this->entity->id(), $input->trailerFile)) {
            $this->entity->setTrailerFile(new Media(
                filePath: $pathTrailerFile,
                mediaStatus: MediaStatus::PROCESSING
            ));
        }

        if ($pathThumbFile = $this->storageFile($this->entity->id(), $input->thumbFile)) {
            $this->entity->setThumbFile(new Image(
                path: $pathThumbFile,
            ));
        }

        if ($pathThumbHalfFile = $this->storageFile($this->entity->id(), $input->thumbHalf)) {
            $this->entity->setThumbHalf(new Image(
                path: $pathThumbHalfFile,
            ));
        }

        if ($pathBannerFile = $this->storageFile($this->entity->id(), $input->bannerFile)) {
            $this->entity->setBannerFile(new Image(
                path: $pathBannerFile,
            ));
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


    private function output(Entity $entity): CreateOutputVideoDTO
    {
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
