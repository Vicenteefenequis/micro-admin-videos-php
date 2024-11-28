<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\Delete\DeleteGenreOutputDto;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteGenreUseCaseUnitTest extends TestCase
{
    public function test_delete()
    {
        $uuid = (string)Uuid::uuid4();

        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('delete')->andReturn(true);

        $mockInputDto = Mockery::mock(GenreInputDto::class, [
            $uuid
        ]);

        $useCase = new DeleteGenreUseCase($mockRepo);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteGenreOutputDto::class, $response);
        $this->assertTrue($response->success);
    }

    public function test_delete_falsy()
    {
        $uuid = (string)Uuid::uuid4();

        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('delete')->andReturn(false);

        $mockInputDto = Mockery::mock(GenreInputDto::class, [
            $uuid
        ]);

        $useCase = new DeleteGenreUseCase($mockRepo);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(DeleteGenreOutputDto::class, $response);
        $this->assertFalse($response->success);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
