<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateOutputDto;

class UpdateCastMemberUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $castMemberRepository)
    {
    }

    public function execute(CastMemberUpdateInputDto $input): CastMemberUpdateOutputDto
    {
        $castMember = $this->castMemberRepository->findById($input->id);

        $castMember->update($input->name);

        $castMemberUpdated = $this->castMemberRepository->update($castMember);

        return new CastMemberUpdateOutputDto(
            id: $castMemberUpdated->id,
            name: $castMemberUpdated->name,
            type: $castMemberUpdated->type->value,
            created_at: $castMemberUpdated->createdAt()
        );

    }
}
