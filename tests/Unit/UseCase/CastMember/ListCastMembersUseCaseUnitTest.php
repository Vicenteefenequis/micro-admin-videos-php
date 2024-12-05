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

class ListCastMembersUseCaseUnitTest extends TestCase
{
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

    protected function mockPagination(array $items = [])
    {
        $this->mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $this->mockPagination->shouldReceive('items')->andReturn($items);
        $this->mockPagination->shouldReceive('total')->andReturn(0);
        $this->mockPagination->shouldReceive('currentPage')->andReturn(0);
        $this->mockPagination->shouldReceive('firstPage')->andReturn(0);
        $this->mockPagination->shouldReceive('lastPage')->andReturn(0);
        $this->mockPagination->shouldReceive('perPage')->andReturn(0);
        $this->mockPagination->shouldReceive('to')->andReturn(0);
        $this->mockPagination->shouldReceive('from')->andReturn(0);

        return $this->mockPagination;
    }
}
