<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\ListCastMembers\{
    ListCastMembersInputDto,
    ListCastMembersOutputDto
};

class ListCastMembersUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $castMemberRepository)
    {
    }

    public function execute(ListCastMembersInputDto $input): ListCastMembersOutputDto
    {
        $result = $this->castMemberRepository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );
        return new ListCastMembersOutputDto(
            items: $result->items(),
            total: $result->total(),
            current_page: $result->currentPage(),
            last_page: $result->lastPage(),
            first_page: $result->firstPage(),
            per_page: $result->perPage(),
            to: $result->to(),
            from: $result->from()
        );
    }
}
