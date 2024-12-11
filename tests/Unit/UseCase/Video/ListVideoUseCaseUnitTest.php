<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\List\DTO\{
    ListInputVideoDTO,
    List0utputVideoDTO
};
use Core\UseCase\Video\List\ListVideoUseCase;

use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListVideoUseCaseUnitTest extends TestCase
{

    public function test_list()
    {
        $uuid = Uuid::random();
        $useCase = new ListVideoUseCase(
            repository: $this->mockRepository()
        );
        $response = $useCase->execute(
            input: $this->mockInput($uuid)
        );

        $this->assertTrue(true);
        $this->assertInstanceOf(List0utputVideoDTO::class, $response);
    }

    private function mockInput(string $uuid)
    {
        return Mockery::mock(ListInputVideoDTO::class, [
            $uuid
        ]);
    }

    private function mockRepository()
    {
        $mockRepo = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->once()->andReturn($this->getEntity());
        return $mockRepo;
    }

    private function getEntity()
    {
        return new Video(
            title: 'title',
            description: 'description',
            yearLaunched: 2020,
            duration: 12,
            opened: true,
            rating: Rating::ER
        );
    }

}
