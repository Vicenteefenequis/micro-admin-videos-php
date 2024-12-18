<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\UseCase\UseCaseTrait;

class ListCastMembersUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testList()
    {
        $mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('paginate')->andReturn($this->mockPagination());

        $mockDtoInput = Mockery::mock(ListCastMembersInputDto::class, [
            'teste',
            'desc',
            1,
            15
        ]);

        $useCase = new ListCastMembersUseCase($mockRepo);
        $response = $useCase->execute($mockDtoInput);

        $this->assertInstanceOf(ListCastMembersOutputDto::class, $response);
        Mockery::close();
    }


}
