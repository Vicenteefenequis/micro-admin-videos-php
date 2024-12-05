<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Entity\CastMember as Entity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
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

    public function testFindById()
    {
        $castMember = Model::factory()->create();
        $response = $this->repository->findById($castMember->id);

        $this->assertInstanceOf(Entity::class, $response);
        $this->assertDatabaseHas('cast_members', ['name' => $castMember->name, 'id' => $castMember->id]);
    }

    public function testFindByIdNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->findById('fake_id');
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertEmpty($response);
    }

    public function testFindAll()
    {
        Model::factory()->count(20)->create();
        $response = $this->repository->findAll();
        $this->assertCount(20, $response);
    }

    public function testPaginate()
    {
        Model::factory()->count(20)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
        $this->assertEquals(20, $response->total());
    }

    public function testUpdate()
    {
        $castMember = Model::factory()->create();

        $entity = new Entity(
            name: 'Teste',
            type: CastMemberType::ACTOR,
            id: new Uuid($castMember->id)
        );
        $response = $this->repository->update($entity);

        $this->assertEquals('Teste', $response->name);
        $this->assertEquals(CastMemberType::ACTOR, $response->type);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->update(new Entity(
            name: 'Teste',
            type: CastMemberType::ACTOR,
        ));
    }
}
