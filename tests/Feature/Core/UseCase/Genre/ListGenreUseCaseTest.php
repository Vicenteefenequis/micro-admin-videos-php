<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\Genre\ListGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListGenreUseCaseTest extends TestCase
{

    public function test_find_by_id()
    {
        $genre = Model::factory()->create();
        $repository = new GenreEloquentRepository(new Model());
        $useCase = new ListGenreUseCase($repository);
        $response = $useCase->execute(
            new GenreInputDto($genre->id)
        );

        $this->assertEquals($genre->id, $response->id);
    }

    public function test_find_by_id_not_found()
    {
        $this->expectException(NotFoundException::class);
        $repository = new GenreEloquentRepository(new Model());
        $useCase = new ListGenreUseCase($repository);
        $useCase->execute(
            new GenreInputDto('fake_id')
        );
    }
}
