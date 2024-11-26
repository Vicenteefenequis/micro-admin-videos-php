<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Genre;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;

class GenreUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = (string)\Ramsey\Uuid\Uuid::uuid4();
        $date = date('Y-m-d H:i:s');
        $genre = new Genre(
            name: 'New Name',
            id: new Uuid($uuid),
            isActive: true,
            createdAt: new DateTime($date),
        );

        $this->assertEquals($uuid, $genre->id());
        $this->assertEquals('New Name', $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertEquals($date, $genre->createdAt());
    }

    public function testAttributesCreate()
    {
        $genre = new Genre(
            name: 'New Name',
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals('New Name', $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertNotEmpty($genre->createdAt());
    }
}
