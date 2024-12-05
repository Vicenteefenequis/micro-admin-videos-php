<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class UpdateCastMemberUseCaseUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_updated()
    {
        $id = RamseyUuid::uuid4();
        $mockEntity = Mockery::mock(CastMember::class, [
            'Name',
            CastMemberType::DIRECTOR,
            new Uuid($id)
        ]);
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('createdAt')->andReturn(date("Y-m-d H:i:s"));

        $repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $repository->shouldReceive('findById')->andReturn($mockEntity);
        $repository->shouldReceive('update')->andReturn($mockEntity);

        $useCase = new UpdateCastMemberUseCase($repository);

        $inputDto = Mockery::mock(CastMemberUpdateInputDto::class, [
            $id,
            'Name',
        ]);

        $output = $useCase->execute($inputDto);

        $this->assertInstanceOf(CastMemberUpdateOutputDto::class, $output);

        $this->assertNotEmpty($output->id);
        $this->assertEquals('Name', $output->name);
        $this->assertEquals(1, $output->type);
        $this->assertNotEmpty($output->created_at);

        Mockery::close();
    }
}
