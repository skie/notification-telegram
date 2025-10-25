<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Message;

use Cake\TelegramNotification\Message\TelegramContact;
use Cake\TestSuite\TestCase;

/**
 * TelegramContact Test
 */
class TelegramContactTest extends TestCase
{
    /**
     * Test create with phone number
     *
     * @return void
     */
    public function testCreateWithPhoneNumber(): void
    {
        $contact = new TelegramContact('123456789');

        $this->assertEquals('123456789', $contact->getPayloadValue('phone_number'));
    }

    /**
     * Test static create
     *
     * @return void
     */
    public function testStaticCreate(): void
    {
        $contact = TelegramContact::create('987654321');

        $this->assertEquals('987654321', $contact->getPayloadValue('phone_number'));
    }

    /**
     * Test firstName method
     *
     * @return void
     */
    public function testFirstNameMethod(): void
    {
        $contact = TelegramContact::create()->firstName('John');

        $this->assertEquals('John', $contact->getPayloadValue('first_name'));
    }

    /**
     * Test lastName method
     *
     * @return void
     */
    public function testLastNameMethod(): void
    {
        $contact = TelegramContact::create()->lastName('Doe');

        $this->assertEquals('Doe', $contact->getPayloadValue('last_name'));
    }

    /**
     * Test vCard method
     *
     * @return void
     */
    public function testVCardMethod(): void
    {
        $contact = TelegramContact::create()->vCard('BEGIN:VCARD...');

        $this->assertEquals('BEGIN:VCARD...', $contact->getPayloadValue('vcard'));
    }

    /**
     * Test complete contact
     *
     * @return void
     */
    public function testCompleteContact(): void
    {
        $contact = TelegramContact::create('123456789')
            ->to(12345)
            ->firstName('John')
            ->lastName('Doe')
            ->vCard('vCard data');

        $array = $contact->toArray();

        $this->assertEquals(12345, $array['chat_id']);
        $this->assertEquals('123456789', $array['phone_number']);
        $this->assertEquals('John', $array['first_name']);
        $this->assertEquals('Doe', $array['last_name']);
        $this->assertEquals('vCard data', $array['vcard']);
    }
}
