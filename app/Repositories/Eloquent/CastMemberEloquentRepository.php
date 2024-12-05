<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember as Model;
use App\Repositories\Presenters\PaginationPresenter;
use Carbon\Traits\Cast;
use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{

    public function __construct(protected Model $model)
    {
    }

    public function insert(CastMember $castMember): CastMember
    {
        $castMemberDb = $this->model->create([
            'id' => $castMember->id(),
            'name' => $castMember->name,
            'type' => $castMember->type->value,
            'created_at' => $castMember->createdAt(),
        ]);

        return $this->toCastMember($castMemberDb);
    }

    public function findById(string $castMemberId): CastMember
    {
        if (!$castMember = $this->model->find($castMemberId)) {
            throw new NotFoundException('CastMember not found with id ' . $castMemberId);
        }
        return $this->toCastMember($castMember);
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
            $query->where('name', 'LIKE', '%' . $filter . '%');
        }
        $query->orderBy('id', $order);
        $paginator = $query->paginate();

        return new PaginationPresenter($paginator);
    }

    public function update(CastMember $castMember): CastMember
    {
        if (!$castMemberDb = $this->model->find($castMember->id())) {
            throw new NotFoundException('CastMember not found with id ' . $castMember->id());
        }

        $castMemberDb->update([
            'name' => $castMember->name,
            'type' => $castMember->type->value,
        ]);


        return $this->toCastMember($castMemberDb);
    }

    public function delete(string $castMemberId): bool
    {
        if(!$castMemberDb = $this->model->find($castMemberId)) {
            throw new NotFoundException('CastMember not found with id ' . $castMemberId);
        }
        return $castMemberDb->delete();
    }

    private function toCastMember(object $object): CastMember
    {
        $entity = new CastMember(
            name: $object->name,
            type: CastMemberType::from($object->type),
            id: new Uuid($object->id),
            createdAt: new DateTime($object->created_at),
        );


        return $entity;
    }
}
