<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\DeleteCastMember\CastMemberDeleteOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteCastMembersUseCaseUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_delete()
    {
        $uuid = Uuid::uuid4()->toString();

        $this->mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->mockRepo->shouldReceive('delete')->andReturn(true);

        $this->mockInputDto = Mockery::mock(CastMemberInputDto::class, [$uuid]);

        $useCase = new DeleteCastMemberUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(CastMemberDeleteOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);
    }
}
