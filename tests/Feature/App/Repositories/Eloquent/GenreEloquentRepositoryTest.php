<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use Core\Domain\Entity\Genre as Entity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
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

    public function testFindAll()
    {
        Model::factory()->count(10)->create();
        $response = $this->repository->findAll();

        $this->assertCount(10, $response);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();

        $this->assertCount(0, $response);
    }

    public function testFindAllByFilter()
    {
        Model::factory()->count(10)->create([
            'name' => 'teste'
        ]);
        Model::factory()->count(10)->create();

        $response = $this->repository->findAll(filter: 'teste');

        $this->assertCount(10, $response);

        $response = $this->repository->findAll();

        $this->assertCount(20, $response);
    }

    public function testPagination()
    {
        Model::factory()->count(60)->create();

        $response = $this->repository->paginate();


        $this->assertEquals(15, count($response->items()));
        $this->assertEquals(60, $response->total());
    }

    public function testPaginationEmpty()
    {

        $response = $this->repository->paginate();


        $this->assertCount(0, $response->items());
        $this->assertEquals(0, $response->total());
    }

    public function testUpdate()
    {
        $nameUpdated = 'NameUpdated';

        $genre = Model::factory()->create();

        $entity = new Entity(
            name: $genre->name,
            id: new Uuid($genre->id),
            isActive: $genre->is_active,
            createdAt: new DateTime($genre->created_at)
        );

        $entity->update($nameUpdated);

        $response = $this->repository->update($entity);

        $this->assertEquals($nameUpdated, $response->name);

        $this->assertDatabaseHas('genres', [
            'name' => $nameUpdated
        ]);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);

        $nameUpdated = 'NameUpdated';

        $genreId = (string)\Ramsey\Uuid\Uuid::uuid4();

        $entity = new Entity(
            name: 'name',
            id: new Uuid($genreId),
            isActive: false,
            createdAt: new DateTime(date('Y-m-d H:i:s'))
        );

        $entity->update($nameUpdated);

        $this->repository->update($entity);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('fake_id');
    }

    public function testDelete()
    {
        $genre = Model::factory()->create();
        $response = $this->repository->delete($genre->id);
        $this->assertTrue($response);
        $this->assertSoftDeleted('genres', [
            'id' => $genre->id
        ]);
    }


}
