<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Tests\TestCase;

class UpdateCategoryUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update()
    {
        $categoryDb = Model::factory()->create();
        $repository = new CategoryEloquentRepository(new Model());
        $useCase = new UpdateCategoryUseCase($repository);
        $response = $useCase->execute(new CategoryUpdateInputDto(
            id: $categoryDb->id,
            name: 'name updated',
        ));

        $this->assertEquals('name updated', $response->name);
        $this->assertEquals($categoryDb->description, $response->description);

        $this->assertDatabaseHas('categories', [
            'name' => $response->name
        ]);
    }
}
