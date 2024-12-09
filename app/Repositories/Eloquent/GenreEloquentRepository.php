<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as Model;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Genre as Entity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Illuminate\Contracts\Queue\EntityNotFoundException;

class GenreEloquentRepository implements GenreRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function insert(Entity $genre): Entity
    {
        $genreDb = $this->model->create([
            'id' => $genre->id(),
            'name' => $genre->name,
            'is_active' => $genre->isActive,
            'created_At' => $genre->createdAt()
        ]);

        if (count($genre->categoriesId) > 0) {
            $genreDb->categories()->sync($genre->categoriesId);
        }

        return $this->toGenre($genreDb);
    }

    public function findById(string $genreId): Entity
    {
        if (!$genreDb = $this->model->find($genreId)) {
            throw new NotFoundException("Genre {$genreId} not found");
        }

        return $this->toGenre($genreDb);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $result = $this->model->where(function ($query) use ($filter) {
            if ($filter) {
                $query->where('name', 'like', '%' . $filter . '%');
            }
        })
            ->orderBy('name', $order)
            ->get();
        return $result->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $query = $this->model;

        if ($filter) {
            $query = $query->where('name', 'LIKE', '%' . $filter . '%');
        }
        $query = $query->orderBy('id', $order);
        $paginator = $query->paginate();

        return new PaginationPresenter($paginator);
    }

    public function update(Entity $genre): Entity
    {
        if (!$genreDb = $this->model->find($genre->id)) {
            throw new NotFoundException("Genre {$genre->id} not found");
        }

        $genreDb->update([
            'name' => $genre->name
        ]);

        if (count($genre->categoriesId) > 0) {
            $genreDb->categories()->sync($genre->categoriesId);
        }

        $genreDb->refresh();

        return $this->toGenre($genreDb);
    }

    public function delete(string $genreId): bool
    {
        if (!$genreDb = $this->model->find($genreId)) {
            throw new NotFoundException("Genre {$genreId} not found");
        }

        return $genreDb->delete();
    }

    private function toGenre(object $object): Entity
    {
        $entity = new Entity(
            name: $object->name,
            id: new Uuid($object->id),
            createdAt: new DateTime($object->created_at),
        );

        ($object->is_active) ? $entity->activate() : $entity->deactivate();

        return $entity;
    }

    public function getIdsListIds(array $genresIds = []): array
    {
        return $this->model->whereIn('id', $genresIds)
            ->pluck('id')
            ->toArray();
    }
}
