<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateOutputDto;

class CreateCastMemberUseCase
{

    public function __construct(protected CastMemberRepositoryInterface $castMemberRepository)
    {
    }

    public function execute(CastMemberCreateInputDto $input): CastMemberCreateOutputDto
    {

        $castMember = new CastMember(
            name: $input->name,
            type: CastMemberType::from($input->type),
        );
        $output = $this->castMemberRepository->insert($castMember);

        return new CastMemberCreateOutputDto(
            id: $output->id,
            name: $output->name,
            type: $output->type->value,
            created_at: $output->createdAt()
        );
    }

}
