<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Message;

use Cake\TelegramNotification\Message\TelegramVenue;
use Cake\TestSuite\TestCase;

/**
 * TelegramVenue Test
 */
class TelegramVenueTest extends TestCase
{
    /**
     * Test create with parameters
     *
     * @return void
     */
    public function testCreateWithParameters(): void
    {
        $venue = new TelegramVenue(40.7128, -74.0060, 'Empire State', 'New York');

        $this->assertEquals(40.7128, $venue->getPayloadValue('latitude'));
        $this->assertEquals(-74.0060, $venue->getPayloadValue('longitude'));
        $this->assertEquals('Empire State', $venue->getPayloadValue('title'));
        $this->assertEquals('New York', $venue->getPayloadValue('address'));
    }

    /**
     * Test static create
     *
     * @return void
     */
    public function testStaticCreate(): void
    {
        $venue = TelegramVenue::create(51.5074, -0.1278, 'Big Ben', 'London');

        $this->assertEquals(51.5074, $venue->getPayloadValue('latitude'));
        $this->assertEquals(-0.1278, $venue->getPayloadValue('longitude'));
    }

    /**
     * Test foursquare methods
     *
     * @return void
     */
    public function testFoursquareMethods(): void
    {
        $venue = TelegramVenue::create()
            ->foursquareId('4sq123')
            ->foursquareType('restaurant');

        $this->assertEquals('4sq123', $venue->getPayloadValue('foursquare_id'));
        $this->assertEquals('restaurant', $venue->getPayloadValue('foursquare_type'));
    }

    /**
     * Test Google Place methods
     *
     * @return void
     */
    public function testGooglePlaceMethods(): void
    {
        $venue = TelegramVenue::create()
            ->googlePlaceId('ChIJ123')
            ->googlePlaceType('museum');

        $this->assertEquals('ChIJ123', $venue->getPayloadValue('google_place_id'));
        $this->assertEquals('museum', $venue->getPayloadValue('google_place_type'));
    }

    /**
     * Test complete venue
     *
     * @return void
     */
    public function testCompleteVenue(): void
    {
        $venue = TelegramVenue::create(40.7128, -74.0060, 'Venue', 'Address')
            ->to(12345)
            ->foursquareId('4sq')
            ->googlePlaceId('gpl');

        $array = $venue->toArray();

        $this->assertEquals(12345, $array['chat_id']);
        $this->assertArrayHasKey('latitude', $array);
        $this->assertArrayHasKey('longitude', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('address', $array);
    }
}
