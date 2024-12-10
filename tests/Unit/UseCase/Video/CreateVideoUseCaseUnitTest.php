<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Enum\Rating;

use Core\UseCase\Video\Create\CreateVideoUseCase as UseCase;
use Core\UseCase\Video\Create\DTO\CreateInputVideoDTO;
use Core\UseCase\Video\Create\DTO\CreateOutputVideoDTO;
use Mockery;

class CreateVideoUseCaseUnitTest extends BaseVideoUseCaseUnitTest
{
    public function test_exec_input_output()
    {
        $this->createUseCase();
        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );

        $this->assertInstanceOf(CreateOutputVideoDTO::class, $response);
    }

    protected function getUseCase(): string
    {
        return UseCase::class;
    }

    protected function nameActionRepository()
    {
        return 'insert';
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
        return Mockery::mock(CreateInputVideoDTO::class, [
            'Tile',
            'Description',
            2020,
            12,
            true,
            Rating::RATE12,
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
