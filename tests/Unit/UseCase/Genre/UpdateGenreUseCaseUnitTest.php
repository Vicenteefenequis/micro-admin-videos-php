<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\DTO\Genre\Update\{
    GenreUpdateInputDto,
    GenreUpdateOutputDto
};
use Core\UseCase\Genre\UpdateGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateGenreUseCaseUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_update()
    {
        $uuid = (string)Uuid::uuid4();

        $useCase = new UpdateGenreUseCase($this->mockRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));

        $response = $useCase->execute($this->mockInputDto($uuid,[$uuid]));

        $this->assertInstanceOf(GenreUpdateOutputDto::class, $response);
    }

    public function test_update_categories_not_found()
    {
        $uuid = (string)Uuid::uuid4();

        $this->expectException(NotFoundException::class);

        $useCase = new UpdateGenreUseCase($this->mockRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));

        $useCase->execute($this->mockInputDto($uuid,[$uuid,'id2']));
    }

    private function mockEntity(string $uuid)
    {

        $mockEntity = Mockery::mock(Genre::class, [
            'new name',
            new \Core\Domain\ValueObject\Uuid($uuid),
            true,
            []
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('addCategory');
        return $mockEntity;
    }

    private function mockRepository(string $uuid)
    {
        $mockEntity = $this->mockEntity($uuid);
        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('update')->andReturn($mockEntity);
        return $mockRepo;
    }

    private function mockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollback');
        return $mockTransaction;
    }

    private function mockCategoryRepository(string $uuid)
    {
        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsListIds')->andReturn([$uuid]);

        return $mockCategoryRepository;
    }


    private function mockInputDto(string $uuid, array $categoriesIds)
    {
        $mockInputDto = Mockery::mock(GenreUpdateInputDto::class, [
            $uuid,
            'name to update',
            $categoriesIds,
        ]);

        return $mockInputDto;
    }
}
