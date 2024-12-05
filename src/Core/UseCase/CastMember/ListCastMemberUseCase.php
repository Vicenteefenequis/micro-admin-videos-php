<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;

class ListCastMemberUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $castMemberRepository)
    {
    }

    public function execute(CastMemberInputDto $input): CastMemberOutputDto
    {
        $castMember = $this->castMemberRepository->findById($input->id);

        return new CastMemberOutputDto(
            id: $castMember->id,
            name: $castMember->name,
            type: $castMember->type->value,
            created_at: $castMember->createdAt()
        );
    }
}
