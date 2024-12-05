<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class ListCastMemberUseCaseUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetById()
    {

        $id = (string)RamseyUuid::uuid4();
        $castMember = Mockery::mock(CastMember::class, [
            'Name',
            CastMemberType::DIRECTOR,
            new Uuid($id)
        ]);
        $castMember->shouldReceive('createdAt')->andReturn(date("Y-m-d H:i:s"));
        $repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $repository->shouldReceive('findById')->andReturn($castMember);
        $useCase = new ListCastMemberUseCase($repository);

        $inputDto = Mockery::mock(CastMemberInputDto::class, [
            $id
        ]);

        $output = $useCase->execute($inputDto);

        $this->assertInstanceOf(CastMemberOutputDto::class, $output);
        $this->assertEquals($id, $output->id);
        $this->assertEquals('Name', $output->name);
        $this->assertEquals(1, $output->type);
        $this->assertNotEmpty($output->created_at);

        Mockery::close();
    }
}
