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
            id: new Uuid($id),
            title: 'new title',
            description: 'description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
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

}
