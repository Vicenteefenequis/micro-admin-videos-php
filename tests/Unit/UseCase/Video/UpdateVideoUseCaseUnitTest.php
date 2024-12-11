<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Enum\Rating;

use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\Update\UpdateVideoUseCase as UseCase;
use Core\UseCase\Video\Update\DTO\{
    UpdateInputVideoDTO,
    UpdateOutputVideoDTO
};
use Mockery;

class UpdateVideoUseCaseUnitTest extends BaseVideoUseCaseUnitTest
{
    public function test_exec_input_output()
    {
        $this->createUseCase();
        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );

        $this->assertInstanceOf(UpdateOutputVideoDTO::class, $response);
    }

    protected function getUseCase(): string
    {
        return UseCase::class;
    }

    protected function nameActionRepository()
    {
        return 'update';
    }


    protected function createMockInputDto(
        array  $categoriesId = [],
        array  $genreIds = [],
        array  $castMembersId = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null
    )
    {
        return Mockery::mock(UpdateInputVideoDTO::class, [
            Uuid::random(),
            'Tile',
            'Description',
            $categoriesId,
            $genreIds,
            $castMembersId,
            $videoFile,
            $trailerFile,
            $thumbFile,
            $thumbHalf,
            $bannerFile,
        ]);
    }


    protected function createUseCase(
        int $timesCallMethodActionRepository = 1,
        int $timesCallMethodUpdateMediaRepository = 1,

        int $timesCallMethodCommitTransaction = 1,
        int $timesCallMethodRollbackTransaction = 0,

        int $timesCallMethodStoreFileStorage = 0,

        int $timesCallMethodDispatchEventManager = 0,
    )
    {
        $this->useCase = new UseCase(
            repository: $this->createMockRepository(
                timesCallAction: $timesCallMethodActionRepository, timesCallUpdateMedia: $timesCallMethodUpdateMediaRepository
            ),
            transaction: $this->createMockTransaction(
                timesCallCommit: $timesCallMethodCommitTransaction, timesCallRollback: $timesCallMethodRollbackTransaction
            ),
            storage: $this->createMockFileStorage(
                timesCall: $timesCallMethodStoreFileStorage
            ),
            eventManager: $this->createMockEventManager(
                timesCall: $timesCallMethodDispatchEventManager
            ),
            repositoryCategory: $this->createMockRepositoryCategory(),
            repositoryGenre: $this->createMockRepositoryGenre(),
            repositoryCastMember: $this->createMockRepositoryCastMembers()
        );
    }


}
