<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;
use Core\UseCase\Genre\ListGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ListGenreUseCaseUnitTest extends TestCase
{
    public function test_list_single()
    {
        $uuid = (string)Uuid::uuid4();
        $mockEntity = Mockery::mock(Genre::class, [
            'new name',
            new \Core\Domain\ValueObject\Uuid($uuid),
            true,
            []
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(GenreInputDto::class,[
            $uuid
        ]);

        $useCase = new ListGenreUseCase($mockRepo);

        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreOutputDto::class, $response);

        Mockery::close();
    }
}
