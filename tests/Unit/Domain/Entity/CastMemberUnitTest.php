<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use DateTime;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = (string)RamseyUuid::uuid4();
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::ACTOR,
            id: new Uuid($uuid),
            createdAt: new DateTime(date('Y-m-d H:i:s')),
        );

        $this->assertEquals($uuid, $castMember->id());
        $this->assertEquals('Name', $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testAttributesNewEntity()
    {
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::DIRECTOR,
        );

        $this->assertNotEmpty($castMember->id());
        $this->assertEquals('Name', $castMember->name);
        $this->assertEquals(CastMemberType::DIRECTOR, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testValidation()
    {
        $this->expectException(EntityValidationException::class);
        new CastMember(
            name: 'ab',
            type: CastMemberType::DIRECTOR,
        );

    }

}
