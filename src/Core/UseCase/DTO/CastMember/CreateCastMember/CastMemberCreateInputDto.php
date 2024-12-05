<?php

namespace Core\UseCase\DTO\CastMember\CreateCastMember;

use Core\Domain\Enum\CastMemberType;

class CastMemberCreateInputDto
{
    public function __construct(
        public string $name,
        public int    $type,
    )
    {
    }
}
