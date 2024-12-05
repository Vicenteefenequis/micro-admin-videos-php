<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Genre\List\{
    ListGenresInputDto,
    ListGenresOutputDto
};
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Genre\ListGenresUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\UseCase\UseCaseTrait;

class ListGenresUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

    public function test_usecase()
    {
        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('paginate')->andReturn($this->mockPagination());

        $mockDtoInput = Mockery::mock(ListGenresInputDto::class, [
            'teste',
            'desc',
            1,
            15
        ]);

        $useCase = new ListGenresUseCase($mockRepo);
        $response = $useCase->execute($mockDtoInput);

        $this->assertInstanceOf(ListGenresOutputDto::class, $response);
        Mockery::close();
    }


}
