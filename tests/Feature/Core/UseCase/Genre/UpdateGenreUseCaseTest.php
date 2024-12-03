<?php

namespace Core\UseCase\Genre;

use App\Models\Category as ModelCategory;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\TransactionDb;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\Update\GenreUpdateInputDto;
use Tests\TestCase;
use Throwable;

class UpdateGenreUseCaseTest extends TestCase
{

    public function test_update()
    {

        $repository = new GenreEloquentRepository(new Model());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());

        $useCase = new UpdateGenreUseCase($repository, new TransactionDb(), $repositoryCategory);

        $genre = Model::factory()->create();
        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesId = $categories->pluck('id')->toArray();


        $useCase->execute(
            new GenreUpdateInputDto(
                id: $genre->id,
                name: 'New Name',
                categoriesIds: $categoriesId
            )
        );

        $this->assertDatabaseHas('genres', [
            'name' => 'New Name'
        ]);

        $this->assertDatabaseCount('category_genre', 10);
    }


    public function test_exception_update_genre_with_categories_ids_invalid()
    {
        $this->expectException(NotFoundException::class);

        $repository = new GenreEloquentRepository(new Model());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());

        $useCase = new UpdateGenreUseCase($repository, new TransactionDb(), $repositoryCategory);

        $genre = Model::factory()->create();
        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesId = $categories->pluck('id')->toArray();
        $categoriesId[] = 'fake_value';


        $useCase->execute(
            new GenreUpdateInputDto(
                id: $genre->id,
                name: 'New Name',
                categoriesIds: $categoriesId
            )
        );
    }

    public function test_update_transactions()
    {
        $repository = new GenreEloquentRepository(new Model());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());

        $useCase = new UpdateGenreUseCase(
            $repository,
            new TransactionDb(),
            $repositoryCategory
        );

        $genre = Model::factory()->create();

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        try {
            $useCase->execute(
                new GenreUpdateInputDto(
                    id: $genre->id,
                    name: 'New Name',
                    categoriesIds: $categoriesIds
                )
            );

            $this->assertDatabaseHas('genres', [
                'name' => 'New Name',
            ]);

            $this->assertDatabaseCount('category_genre', 10);
        } catch (\Throwable $th) {
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }
}
