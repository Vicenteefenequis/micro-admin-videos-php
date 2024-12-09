<?php

namespace Domain\Notification;

use Core\Domain\Notification\Notification;
use Tests\TestCase;

class NotificationUnitTest extends TestCase
{
    public function testGetErrors()
    {
        $notification = new Notification();
        $errors = $notification->getErrors();
        $this->assertIsArray($errors);
    }

    public function testAddErrors()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required'
        ]);

        $errors = $notification->getErrors();
        $this->assertIsArray($errors);
        $this->assertCount(1, $errors);
    }

    public function testHasErrors()
    {
        $notification = new Notification();

        $this->assertFalse($notification->hasErrors());
        $notification->addError([
            'context' => 'video',
            'message' => 'video title is required'
        ]);

        $this->assertTrue($notification->hasErrors());
    }

    public function testMessage()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'title is required'
        ]);
        $notification->addError([
            'context' => 'video',
            'message' => 'description is required'
        ]);
        $message = $notification->messages();
        $this->assertEquals('video: title is required,video: description is required,', $message);
        $this->assertIsString($message);
    }

    public function testMessageFilterContext()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'video',
            'message' => 'title is required'
        ]);
        $notification->addError([
            'context' => 'category',
            'message' => 'name is required'
        ]);
        $message = $notification->messages(
            context: 'video'
        );
        $this->assertEquals('video: title is required,', $message);
        $this->assertIsString($message);
    }

}
