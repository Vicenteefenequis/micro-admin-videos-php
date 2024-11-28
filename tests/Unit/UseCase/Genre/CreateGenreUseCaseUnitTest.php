<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\DTO\Genre\Create\{
    GenreCreateInputDto,
    GenreCreateOutputDto
};
use Core\UseCase\Genre\CreateGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateGenreUseCaseUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create()
    {
        $uuid = (string)Uuid::uuid4();

        $useCase = new CreateGenreUseCase($this->mockRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));

        $response = $useCase->execute($this->mockInputDto([$uuid]));

        $this->assertInstanceOf(GenreCreateOutputDto::class, $response);
    }

    public function test_categories_not_found()
    {
        $uuid = (string)Uuid::uuid4();

        $this->expectException(NotFoundException::class);

        $useCase = new CreateGenreUseCase($this->mockRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));

        $useCase->execute($this->mockInputDto([$uuid,'id2']));
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
        return $mockEntity;
    }

    private function mockRepository(string $uuid)
    {
        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn($this->mockEntity($uuid));
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


    private function mockInputDto(array $categoriesIds)
    {
        $mockInputDto = Mockery::mock(GenreCreateInputDto::class, [
            'name',
            $categoriesIds,
            true
        ]);
        return $mockInputDto;
    }
}
