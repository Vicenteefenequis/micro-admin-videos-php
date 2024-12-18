<?php

namespace Core\UseCase\DTO\CastMember\CreateCastMember;

use Core\Domain\Enum\CastMemberType;

class CastMemberCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $type,
        public string $created_at,
    )
    {
    }
}
