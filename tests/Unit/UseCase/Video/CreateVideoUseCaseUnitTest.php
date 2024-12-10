<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\Video\Create\CreateVideoUseCase as UseCase;
use Core\UseCase\Video\Create\DTO\CreateInputVideoDTO;
use Core\UseCase\Video\Create\DTO\CreateOutputVideoDTO;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateVideoUseCaseUnitTest extends TestCase
{
    protected UseCase $useCase;

    protected function setUp(): void
    {
        $this->useCase = new UseCase(
            repository: $this->createMockRepository(),
            transaction: $this->createMockTransaction(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            repositoryCategory: $this->createMockRepositoryCategory(),
            repositoryGenre: $this->createMockRepositoryGenre(),
            repositoryCastMember: $this->createMockRepositoryCastMembers()
        );
        parent::setUp();
    }

    public function test_exec_input_output()
    {
        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );

        $this->assertInstanceOf(CreateOutputVideoDTO::class, $response);
    }

    public function test_exception_categories_ids()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage('Category 1 not found');

        $this->useCase->execute(
            input: $this->createMockInputDto(categoriesId: ['1'])
        );
    }

    public function test_exception_message_categories_ids()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage('Categories 1, 2 not found');

        $this->useCase->execute(
            input: $this->createMockInputDto(categoriesId: ['1', '2'])
        );
    }

    private function createMockRepository()
    {
        $mockRepo = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn($this->createMockEntity());
        $mockRepo->shouldReceive('updateMedia')->andReturn($this->createMockEntity());
        return $mockRepo;
    }

    private function createMockRepositoryCategory(array $categoriesResponse = [])
    {
        $mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('getIdsListIds')->andReturn($categoriesResponse);
        return $mockRepo;
    }

    private function createMockRepositoryGenre(array $genresResponse = [])
    {
        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('getIdsListIds')->andReturn($genresResponse);
        return $mockRepo;
    }

    private function createMockRepositoryCastMembers(array $castMembers = [])
    {
        $mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('getIdsListIds')->andReturn($castMembers);
        return $mockRepo;
    }


    private function createMockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollback');
        return $mockTransaction;
    }

    private function createMockFileStorage()
    {
        $mockFileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $mockFileStorage->shouldReceive('store')->andReturn('path/file.png');
        return $mockFileStorage;
    }

    private function createMockEventManager()
    {
        $mockEventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockEventManager->shouldReceive('dispatch');
        return $mockEventManager;
    }

    private function createMockInputDto(
        array $categoriesId = [],
        array $genreIds = [],
        array $castMembersId = [],
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
            $castMembersId
        ]);
    }

    private function createMockEntity()
    {
        return Mockery::mock(Video::class, [
            'Title', 'Description', 2020, 12, true, Rating::RATE12
        ]);
    }
}
