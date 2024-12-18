<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category as ModelCategory;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\TransactionDb;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\Create\GenreCreateInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Tests\TestCase;
use Throwable;

class CreateGenreUseCaseTest extends TestCase
{

    public function test_insert()
    {

        $repository = new GenreEloquentRepository(new Model());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());

        $useCase = new CreateGenreUseCase($repository, new TransactionDb(), $repositoryCategory);

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesId = $categories->pluck('id')->toArray();


        $useCase->execute(
            new GenreCreateInputDto(
                name: 'teste',
                categoriesId: $categoriesId
            )
        );

        $this->assertDatabaseHas('genres', [
            'name' => 'teste'
        ]);

        $this->assertDatabaseCount('category_genre', 10);
    }


    public function test_exception_insert_genre_with_categories_ids_invalid()
    {
        $this->expectException(NotFoundException::class);

        $repository = new GenreEloquentRepository(new Model());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());

        $useCase = new CreateGenreUseCase($repository, new TransactionDb(), $repositoryCategory);

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesId = $categories->pluck('id')->toArray();

        $categoriesId[] = 'fake_id';

        $useCase->execute(
            new GenreCreateInputDto(
                name: 'teste',
                categoriesId: $categoriesId
            )
        );
    }

    public function test_insert_transactions()
    {
        $repository = new GenreEloquentRepository(new Model());
        $repositoryCategory = new CategoryEloquentRepository(new ModelCategory());

        $useCase = new CreateGenreUseCase(
            $repository,
            new TransactionDb(),
            $repositoryCategory
        );

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        try {
            $useCase->execute(
                new GenreCreateInputDto(
                    name: 'teste',
                    categoriesId: $categoriesIds
                )
            );

            $this->assertDatabaseHas('genres', [
                'name' => 'teste',
            ]);

            $this->assertDatabaseCount('category_genre', 10);
        } catch (\Throwable $th) {
            //throw $th;
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }
}
