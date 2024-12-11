<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Paginate\DTO\PaginateInputVideoDTO;
use Core\UseCase\Video\Paginate\DTO\PaginateOutputVideoDTO;
use Core\UseCase\Video\Paginate\ListVideosUseCase;
use Mockery;
use stdClass;
use Tests\TestCase;
use Tests\Unit\UseCase\UseCaseTrait;

class ListVideosUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

    public function test_list_paginate()
    {
        $useCase = new ListVideosUseCase(
            repository: $this->mockRepository()
        );

        $response = $useCase->execute(
            input: $this->mockInput()
        );

        $this->assertInstanceOf(PaginateOutputVideoDTO::class, $response);

        Mockery::close();
    }

    private function mockInput()
    {
        return Mockery::mock(PaginateInputVideoDTO::class, [
            'teste',
            'desc',
            1,
            15
        ]);
    }

    private function mockRepository()
    {
        $mockRepo = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('paginate')->once()->andReturn($this->mockPagination());
        return $mockRepo;
    }

}
