<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Message;

use Cake\TelegramNotification\Message\TelegramLocation;
use Cake\TestSuite\TestCase;

/**
 * TelegramLocation Test
 */
class TelegramLocationTest extends TestCase
{
    /**
     * Test create with coordinates
     *
     * @return void
     */
    public function testCreateWithCoordinates(): void
    {
        $location = new TelegramLocation(40.7128, -74.0060);

        $this->assertEquals(40.7128, $location->getPayloadValue('latitude'));
        $this->assertEquals(-74.0060, $location->getPayloadValue('longitude'));
    }

    /**
     * Test static create
     *
     * @return void
     */
    public function testStaticCreate(): void
    {
        $location = TelegramLocation::create(40.7128, -74.0060);

        $this->assertEquals(40.7128, $location->getPayloadValue('latitude'));
        $this->assertEquals(-74.0060, $location->getPayloadValue('longitude'));
    }

    /**
     * Test latitude method
     *
     * @return void
     */
    public function testLatitudeMethod(): void
    {
        $location = TelegramLocation::create()->latitude(51.5074);

        $this->assertEquals(51.5074, $location->getPayloadValue('latitude'));
    }

    /**
     * Test longitude method
     *
     * @return void
     */
    public function testLongitudeMethod(): void
    {
        $location = TelegramLocation::create()->longitude(-0.1278);

        $this->assertEquals(-0.1278, $location->getPayloadValue('longitude'));
    }

    /**
     * Test to method
     *
     * @return void
     */
    public function testToMethod(): void
    {
        $location = TelegramLocation::create()->to(12345);

        $this->assertEquals(12345, $location->getPayloadValue('chat_id'));
        $this->assertFalse($location->toNotGiven());
    }

    /**
     * Test options method
     *
     * @return void
     */
    public function testOptionsMethod(): void
    {
        $location = TelegramLocation::create()->options(['horizontal_accuracy' => 100]);

        $this->assertEquals(100, $location->getPayloadValue('horizontal_accuracy'));
    }
}
