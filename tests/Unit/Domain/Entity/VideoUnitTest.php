<?php

namespace Domain\Entity;

use Core\Domain\Enum\Rating;
use Core\Domain\ValueObject\Uuid;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Tests\TestCase;
use Core\Domain\Entity\Video;

class VideoUnitTest extends TestCase
{
    public function testAttributes()
    {
        $id = (string)RamseyUuid::uuid4();
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($id),
            published: false
        );

        $this->assertEquals($id, $video->id());
        $this->assertEquals('new title', $video->title);
        $this->assertEquals('description', $video->description);
        $this->assertEquals(2029, $video->yearLaunched);
        $this->assertEquals(12, $video->duration);
        $this->assertTrue($video->opened);
        $this->assertEquals(Rating::RATE12, $video->rating);
        $this->assertFalse($video->published);
    }


    public function testId()
    {
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            published: false
        );
        $this->assertNotEmpty($video->id());
    }

    public function testAddCategoryId()
    {
        $categoryId = (string)RamseyUuid::uuid4();
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            published: false
        );

        $this->assertCount(0, $video->categoriesId);

        $video->addCategoryId(
            categoryId: $categoryId
        );
        $video->addCategoryId(
            categoryId: $categoryId
        );


        $this->assertCount(2, $video->categoriesId);
    }

    public function testRemoveCategoryId()
    {
        $categoryId = (string)RamseyUuid::uuid4();
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            published: false
        );

        $this->assertCount(0, $video->categoriesId);

        $video->addCategoryId(
            categoryId: $categoryId
        );
        $video->addCategoryId(
            categoryId: 'uuid'
        );

        $this->assertCount(2, $video->categoriesId);

        $video->removeCategoryId($categoryId);


        $this->assertCount(1, $video->categoriesId);
    }


    public function testAddGenreId()
    {
        $genreId = (string)RamseyUuid::uuid4();
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            published: false
        );

        $this->assertCount(0, $video->genresId);

        $video->addGenreId(
            genreId: $genreId
        );
        $video->addGenreId(
            genreId: $genreId
        );


        $this->assertCount(2, $video->genresId);
    }

    public function testRemoveGenreId()
    {
        $genreId = (string)RamseyUuid::uuid4();
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            published: false
        );

        $this->assertCount(0, $video->genresId);

        $video->addGenreId(
            genreId: $genreId
        );
        $video->addGenreId(
            genreId: 'uuid'
        );

        $this->assertCount(2, $video->genresId);

        $video->removeGenreId($genreId);


        $this->assertCount(1, $video->genresId);
    }

    public function testAddCastMemberId()
    {
        $castMemberId = (string)RamseyUuid::uuid4();
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            published: false
        );

        $this->assertCount(0, $video->castMembersId);

        $video->addCastMemberId(
            castMemberId: $castMemberId
        );
        $video->addCastMemberId(
            castMemberId: $castMemberId
        );


        $this->assertCount(2, $video->castMembersId);
    }

    public function testRemoveCastMemberId()
    {
        $castMemberId = (string)RamseyUuid::uuid4();
        $video = new Video(
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            published: false
        );

        $this->assertCount(0, $video->castMembersId);

        $video->addCastMemberId(
            castMemberId: $castMemberId
        );
        $video->addCastMemberId(
            castMemberId: 'uuid'
        );

        $this->assertCount(2, $video->castMembersId);

        $video->removeCastMemberId($castMemberId);


        $this->assertCount(1, $video->castMembersId);
    }

}
