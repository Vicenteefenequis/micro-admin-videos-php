<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateCastMemberUseCaseUnitTest extends TestCase
{

    public function test_insert()
    {
        $mockEntity = Mockery::mock(CastMember::class, [
            'Name',
            CastMemberType::DIRECTOR
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date("Y-m-d H:i:s"));
        $repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $repository->shouldReceive('insert')->andReturn($mockEntity);
        $useCase = new CreateCastMemberUseCase($repository);

        $inputDto = Mockery::mock(CastMemberCreateInputDto::class, [
            'Name',
            1
        ]);

        $output = $useCase->execute($inputDto);

        $this->assertInstanceOf(CastMemberCreateOutputDto::class, $output);

        $this->assertNotEmpty($output->id);
        $this->assertEquals('Name', $output->name);
        $this->assertEquals(1, $output->type);
        $this->assertNotEmpty($output->created_at);
    }
}
