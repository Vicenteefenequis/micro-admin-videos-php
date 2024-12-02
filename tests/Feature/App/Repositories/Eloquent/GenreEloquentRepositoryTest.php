<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use Core\Domain\Entity\Genre as Entity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Illuminate\Contracts\Queue\EntityNotFoundException;
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

    public function testInsertWithRelationships()
    {
        $categories = Category::factory()->count(4)->create();

        $entity = new Entity(
            name: 'Teste'
        );

        foreach ($categories as $category) {
            $entity->addCategory($category->id);
        }

        $response = $this->repository->insert($entity);

        $this->assertDatabaseHas('genres', [
            'id' => $response->id(),
        ]);

        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testNotFoundById()
    {
        $this->expectException(NotFoundException::class);
        $genre_id = 'fake_value';
        $this->repository->findById($genre_id);
    }

    public function testFindById()
    {
        $genre = Model::factory()->create();

        $response = $this->repository->findById($genre->id);

        $this->assertEquals($genre->id, $response->id);
        $this->assertEquals($genre->name, $response->name);
    }

}
