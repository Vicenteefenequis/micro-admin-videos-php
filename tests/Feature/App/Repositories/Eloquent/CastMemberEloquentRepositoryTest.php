<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Entity\CastMember as Entity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Tests\TestCase;

class CastMemberEloquentRepositoryTest extends TestCase
{
    protected CastMemberEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberEloquentRepository(new Model());
    }

    public function testCheckImplementInterface()
    {
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new Entity(
            name: 'Teste',
            type: CastMemberType::ACTOR
        );
        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(Entity::class, $response);
        $this->assertEquals('Teste', $response->name);
        $this->assertEquals(CastMemberType::ACTOR, $response->type);
        $this->assertNotEmpty($response->id());
        $this->assertNotEmpty($response->createdAt());
        $this->assertDatabaseHas('cast_members', ['name' => $entity->name, 'id' => $entity->id]);
    }
}
