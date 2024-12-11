<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\DeleteVideoUseCase;
use Core\UseCase\Video\Delete\DTO\DeleteInputVideoDTO;
use Core\UseCase\Video\Delete\DTO\DeleteOutputVideoDTO;
use Mockery;
use Ramsey\Uuid\Nonstandard\Uuid;
use stdClass;
use Tests\TestCase;

class DeleteVideoUseCaseUnitTest extends TestCase
{

    public function test_delete()
    {
        $uuid = Uuid::uuid4()->toString();

        $this->mockRepo = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $this->mockRepo->shouldReceive('delete')->once()->andReturn(true);

        $this->mockInputDto = Mockery::mock(DeleteInputVideoDTO::class, [$uuid]);

        $useCase = new DeleteVideoUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(DeleteOutputVideoDTO::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);
    }

}
