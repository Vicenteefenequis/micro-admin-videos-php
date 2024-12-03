<?php

namespace Core\UseCase\Genre;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Tests\TestCase;

class DeleteGenreUseCaseTest extends TestCase
{

    public function test_delete()
    {
        $genre = Model::factory()->create();
        $repository = new GenreEloquentRepository(new Model());
        $useCase = new DeleteGenreUseCase($repository);
        $response = $useCase->execute(
            new GenreInputDto($genre->id)
        );

        $this->assertTrue($response->success);
        $this->assertSoftDeleted('genres', [
           'id' => $genre->id
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(NotFoundException::class);
        $repository = new GenreEloquentRepository(new Model());
        $useCase = new DeleteGenreUseCase($repository);
        $useCase->execute(
            new GenreInputDto('fake_id')
        );
    }
}
