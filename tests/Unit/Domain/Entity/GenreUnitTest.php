<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityValidationException;
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

    public function testDeactivate()
    {
        $genre = new Genre(name: 'Teste');

        $this->assertTrue($genre->isActive);

        $genre->deactivate();

        $this->assertFalse($genre->isActive);
    }

    public function testActivate()
    {
        $genre = new Genre(
            name: 'New Name',
            isActive: false,
        );
        $this->assertFalse($genre->isActive);
        $genre->activate();
        $this->assertTrue($genre->isActive);
    }

    public function testUpdate()
    {
        $genre = new Genre(
            name: 'New Name',
        );

        $genre->update(
            name: 'Name Updated',
        );

        $this->assertEquals('Name Updated', $genre->name);
    }

    public function testEntityExceptions()
    {
        $this->expectException(EntityValidationException::class);
        new Genre(
            name: 'N',
        );
    }

    public function testEntityUpdateExceptions()
    {
        $this->expectException(EntityValidationException::class);
        $genre = new Genre(
            name: 'New Name',
        );

        $genre->update(
            name: 'N',
        );
    }

    public function testAddCategoryToGenre()
    {
        $categoryId = (string)\Ramsey\Uuid\Uuid::uuid4();
        $genre = new Genre(
            name: 'New Name',
        );

        $this->assertIsArray($genre->categoriesId);
        $this->assertCount(0, $genre->categoriesId);
        $genre->addCategory(
            categoryId: $categoryId,
        );
        $genre->addCategory(
            categoryId: $categoryId,
        );

        $this->assertCount(2, $genre->categoriesId);
    }

    public function testRemoveCategoryToGenre()
    {
        $categoryId = (string)\Ramsey\Uuid\Uuid::uuid4();
        $categoryId2 = (string)\Ramsey\Uuid\Uuid::uuid4();
        $genre = new Genre(
            name: 'New Name',
            categoriesId: [
                $categoryId,
                $categoryId2
            ]
        );


        $this->assertCount(2, $genre->categoriesId);

        $genre->removeCategory(
            categoryId: $categoryId,
        );

        $this->assertCount(1, $genre->categoriesId);
        $this->assertEquals($categoryId2, $genre->categoriesId[1]);
    }
}
