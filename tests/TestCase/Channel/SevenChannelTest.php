<?php
declare(strict_types=1);

namespace Cake\SevenNotification\Test\TestCase\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Notification\Exception\CouldNotSendNotification;
use Cake\Notification\Notification;
use Cake\SevenNotification\Channel\SevenChannel;
use Cake\SevenNotification\Message\SevenMessage;
use Cake\TestSuite\TestCase;
use ReflectionClass;
use Seven\Api\Resource\Sms\Sms;
use Seven\Api\Resource\Sms\SmsResource;

/**
 * SevenChannel Test Case
 */
class SevenChannelTest extends TestCase
{
    /**
     * Test channel requires API key
     *
     * @return void
     */
    public function testRequiresApiKey(): void
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage("Channel 'seven' is missing required credential: api_key");

        new SevenChannel([]);
    }

    /**
     * Test send with string message
     *
     * @return void
     */
    public function testSendWithStringMessage(): void
    {
        $channel = new SevenChannel(['api_key' => 'test-key']);

        $mockSms = $this->createMock(Sms::class);
        $mockSmsResource = $this->createMock(SmsResource::class);
        $mockSmsResource->expects($this->once())
            ->method('dispatch')
            ->willReturn($mockSms);

        $reflection = new ReflectionClass($channel);
        $property = $reflection->getProperty('smsResource');
        $property->setAccessible(true);
        $property->setValue($channel, $mockSmsResource);

        $entity = $this->createMock(TestRoutableEntity::class);
        $entity->method('routeNotificationForSeven')->willReturn('+491234567890');

        $notification = $this->createMock(TestSevenNotification::class);
        $notification->method('toSeven')->willReturn('Test message');

        $result = $channel->send($entity, $notification);
        $this->assertInstanceOf(Sms::class, $result);
    }

    /**
     * Test send with SevenMessage object
     *
     * @return void
     */
    public function testSendWithSevenMessage(): void
    {
        $channel = new SevenChannel(['api_key' => 'test-key']);

        $mockSms = $this->createMock(Sms::class);
        $mockSmsResource = $this->createMock(SmsResource::class);
        $mockSmsResource->expects($this->once())
            ->method('dispatch')
            ->willReturn($mockSms);

        $reflection = new ReflectionClass($channel);
        $property = $reflection->getProperty('smsResource');
        $property->setAccessible(true);
        $property->setValue($channel, $mockSmsResource);

        $entity = $this->createMock(EntityInterface::class);

        $message = SevenMessage::create()
            ->content('Test')
            ->to('+491234567890');

        $notification = $this->createMock(TestSevenNotification::class);
        $notification->method('toSeven')->willReturn($message);

        $result = $channel->send($entity, $notification);
        $this->assertInstanceOf(Sms::class, $result);
    }

    /**
     * Test routing from entity method
     *
     * @return void
     */
    public function testRoutingFromEntityMethod(): void
    {
        $channel = new SevenChannel(['api_key' => 'test-key']);

        $mockSms = $this->createMock(Sms::class);
        $mockSmsResource = $this->createMock(SmsResource::class);
        $mockSmsResource->expects($this->once())
            ->method('dispatch')
            ->willReturn($mockSms);

        $reflection = new ReflectionClass($channel);
        $property = $reflection->getProperty('smsResource');
        $property->setAccessible(true);
        $property->setValue($channel, $mockSmsResource);

        $entity = $this->createMock(TestRoutableEntity::class);
        $entity->method('routeNotificationForSeven')->willReturn('+491234567890');

        $notification = $this->createMock(TestSevenNotification::class);
        $notification->method('toSeven')->willReturn(SevenMessage::create('Test'));

        $result = $channel->send($entity, $notification);
        $this->assertInstanceOf(Sms::class, $result);
    }

    /**
     * Test missing routing information
     *
     * @return void
     */
    public function testMissingRoutingInformation(): void
    {
        $channel = new SevenChannel(['api_key' => 'test-key']);

        $entity = $this->createMock(EntityInterface::class);

        $notification = $this->createMock(TestSevenNotification::class);
        $notification->method('toSeven')->willReturn(SevenMessage::create('Test'));

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage("Channel 'seven' requires routing information");

        $channel->send($entity, $notification);
    }

    /**
     * Test send returns null when notification does not have toSeven method
     *
     * @return void
     */
    public function testSendReturnsNullWithoutToSevenMethod(): void
    {
        $channel = new SevenChannel(['api_key' => 'test-key']);

        $entity = $this->createMock(EntityInterface::class);
        $notification = $this->createMock(Notification::class);

        $result = $channel->send($entity, $notification);
        $this->assertNull($result);
    }

    /**
     * Test send returns null when toSeven returns null
     *
     * @return void
     */
    public function testSendReturnsNullWhenToSevenReturnsNull(): void
    {
        $channel = new SevenChannel(['api_key' => 'test-key']);

        $entity = $this->createMock(EntityInterface::class);

        $notification = $this->createMock(TestSevenNotification::class);
        $notification->method('toSeven')->willReturn(null);

        $result = $channel->send($entity, $notification);
        $this->assertNull($result);
    }
}
