<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\DeleteCastMember\CastMemberDeleteOutputDto;

class DeleteCastMemberUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $castMemberRepository)
    {
    }

    public function execute(CastMemberInputDto $input): CastMemberDeleteOutputDto
    {
        return new CastMemberDeleteOutputDto(
            success: $this->castMemberRepository->delete($input->id)
        );
    }
}
