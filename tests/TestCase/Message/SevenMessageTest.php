<?php
declare(strict_types=1);

namespace Cake\SevenNotification\Test\TestCase\Message;

use Cake\SevenNotification\Message\SevenMessage;
use Cake\TestSuite\TestCase;

/**
 * SevenMessage Test Case
 */
class SevenMessageTest extends TestCase
{
    /**
     * Test create method
     *
     * @return void
     */
    public function testCreate(): void
    {
        $message = SevenMessage::create('Test message');
        $this->assertInstanceOf(SevenMessage::class, $message);
        $this->assertEquals('Test message', $message->getPayloadValue('text'));
    }

    /**
     * Test content method
     *
     * @return void
     */
    public function testContent(): void
    {
        $message = SevenMessage::create()->content('Hello World');
        $this->assertEquals('Hello World', $message->getPayloadValue('text'));
    }

    /**
     * Test to method
     *
     * @return void
     */
    public function testTo(): void
    {
        $message = SevenMessage::create()->to('+491234567890');
        $this->assertEquals('+491234567890', $message->getPayloadValue('to'));
        $this->assertTrue($message->hasTo());
    }

    /**
     * Test from method
     *
     * @return void
     */
    public function testFrom(): void
    {
        $message = SevenMessage::create()->from('MyApp');
        $this->assertEquals('MyApp', $message->getPayloadValue('from'));
    }

    /**
     * Test flash method
     *
     * @return void
     */
    public function testFlash(): void
    {
        $message = SevenMessage::create()->flash();
        $this->assertTrue($message->getPayloadValue('flash'));
    }

    /**
     * Test delay method
     *
     * @return void
     */
    public function testDelay(): void
    {
        $timestamp = time() + 3600;
        $message = SevenMessage::create()->delay($timestamp);
        $this->assertEquals($timestamp, $message->getPayloadValue('delay'));
    }

    /**
     * Test foreignId method
     *
     * @return void
     */
    public function testForeignId(): void
    {
        $message = SevenMessage::create()->foreignId('order-123');
        $this->assertEquals('order-123', $message->getPayloadValue('foreign_id'));
    }

    /**
     * Test label method
     *
     * @return void
     */
    public function testLabel(): void
    {
        $message = SevenMessage::create()->label('campaign-2024');
        $this->assertEquals('campaign-2024', $message->getPayloadValue('label'));
    }

    /**
     * Test performanceTracking method
     *
     * @return void
     */
    public function testPerformanceTracking(): void
    {
        $message = SevenMessage::create()->performanceTracking();
        $this->assertTrue($message->getPayloadValue('performance_tracking'));
    }

    /**
     * Test fluent interface
     *
     * @return void
     */
    public function testFluentInterface(): void
    {
        $message = SevenMessage::create()
            ->content('Test SMS')
            ->to('+491234567890')
            ->from('TestApp')
            ->label('test')
            ->performanceTracking();

        $this->assertEquals('Test SMS', $message->getPayloadValue('text'));
        $this->assertEquals('+491234567890', $message->getPayloadValue('to'));
        $this->assertEquals('TestApp', $message->getPayloadValue('from'));
        $this->assertEquals('test', $message->getPayloadValue('label'));
        $this->assertTrue($message->getPayloadValue('performance_tracking'));
    }

    /**
     * Test toArray method
     *
     * @return void
     */
    public function testToArray(): void
    {
        $message = SevenMessage::create()
            ->content('Test')
            ->to('+491234567890')
            ->from('App');

        $array = $message->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test', $array['text']);
        $this->assertEquals('+491234567890', $array['to']);
        $this->assertEquals('App', $array['from']);
    }

    /**
     * Test hasTo returns false when no recipient
     *
     * @return void
     */
    public function testHasToReturnsFalse(): void
    {
        $message = SevenMessage::create('Test');
        $this->assertFalse($message->hasTo());
    }
}
