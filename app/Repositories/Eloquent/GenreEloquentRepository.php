<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as Model;
use Core\Domain\Entity\Genre as Entity;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class GenreEloquentRepository implements GenreRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function insert(Entity $genre): Entity
    {
        $register = $this->model->create([
            'id' => $genre->id(),
            'name' => $genre->name,
            'is_active' => $genre->isActive,
            'created_At' => $genre->createdAt()
        ]);

        return $this->toGenre($register);
    }

    public function findById(string $genreId): Entity
    {
        // TODO: Implement findById() method.
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        // TODO: Implement findAll() method.
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        // TODO: Implement paginate() method.
    }

    public function update(Entity $genre): Entity
    {
        // TODO: Implement update() method.
    }

    public function delete(string $genreId): bool
    {
        // TODO: Implement delete() method.
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
}
