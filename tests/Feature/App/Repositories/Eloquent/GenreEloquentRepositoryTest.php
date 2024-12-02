<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as Model;
use Core\Domain\Entity\Genre as Entity;
use Core\Domain\Repository\GenreRepositoryInterface;
use Tests\TestCase;

class GenreEloquentRepositoryTest extends TestCase
{

    protected GenreEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreEloquentRepository(new Model());
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new Entity(
            name: 'New Genre'
        );
        $response = $this->repository->insert($entity);

        $this->assertEquals($entity->name, $response->name);
        $this->assertEquals($entity->id, $response->id());

        $this->assertDatabaseHas('genres', [
            'id' => $response->id(),
        ]);
    }

    public function testInsertDeactivate()
    {
        $entity = new Entity(
            name: 'New Genre'
        );
        $entity->deactivate();
        $response = $this->repository->insert($entity);

        $this->assertEquals($entity->name, $response->name);
        $this->assertEquals($entity->id, $response->id());

        $this->assertDatabaseHas('genres', [
            'id' => $response->id(),
            'is_active' => false
        ]);
    }

}
