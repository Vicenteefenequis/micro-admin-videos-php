<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Tests\TestCase;
use Throwable;

class CategoryEloquentRepositoryTest extends TestCase
{
    protected CategoryEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new Model());

    }

    public function testInsert()
    {
        $entity = new EntityCategory(
            name: 'teste'
        );
        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', [
            'name' => $entity->name
        ]);
    }

    public function testFindById()
    {
        $category = Model::factory()->create();
        $response = $this->repository->findById($category->id);

        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertEquals($category->id, $response->id());
    }

    public function testFindByIdNotFound()
    {
        try {
            $this->repository->findById('fake_id');
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testFindAll()
    {
        Model::factory()->count(10)->create();

        $response = $this->repository->findAll();

        $this->assertCount(10, $response);
    }
}
