<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Channel;

use Cake\Notification\Exception\CouldNotSendNotification;
use Cake\TelegramNotification\Channel\TelegramChannel;
use Cake\TestSuite\TestCase;
use Longman\TelegramBot\Telegram;

/**
 * TelegramChannel Test
 */
class TelegramChannelTest extends TestCase
{
    /**
     * Test constructor throws exception without token
     *
     * @return void
     */
    public function testConstructorThrowsExceptionWithoutToken(): void
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('missing required credential: token');

        new TelegramChannel([]);
    }

    /**
     * Test send with TelegramMessage
     *
     * @return void
     */
    public function testSendWithTelegramMessage(): void
    {
        $mockTelegram = $this->createMock(Telegram::class);

        $channel = new TelegramChannel([
            'token' => 'test-token',
        ], $mockTelegram);

        $notifiable = new TestRoutableEntity('123456');
        $notification = new TestTelegramMessageNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertIsObject($result);
    }

    /**
     * Test send with string message
     *
     * @return void
     */
    public function testSendWithStringMessage(): void
    {
        $mockTelegram = $this->createMock(Telegram::class);

        $channel = new TelegramChannel([
            'token' => 'test-token',
        ], $mockTelegram);

        $notifiable = new TestRoutableEntity('123456');
        $notification = new TestStringNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertIsObject($result);
    }

    /**
     * Test send with null message
     *
     * @return void
     */
    public function testSendWithNullMessage(): void
    {
        $mockTelegram = $this->createMock(Telegram::class);

        $channel = new TelegramChannel([
            'token' => 'test-token',
        ], $mockTelegram);

        $notifiable = new TestRoutableEntity('123456');
        $notification = new TestTelegramNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertNull($result);
    }

    /**
     * Test send returns null without chat ID
     *
     * @return void
     */
    public function testSendReturnsNullWithoutChatId(): void
    {
        $mockTelegram = $this->createMock(Telegram::class);

        $channel = new TelegramChannel([
            'token' => 'test-token',
        ], $mockTelegram);

        $notifiable = new TestRoutableEntity(null);
        $notification = new TestTelegramMessageNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertNull($result);
    }

    /**
     * Test send with conditional sending
     *
     * @return void
     */
    public function testSendWithConditionalSending(): void
    {
        $mockTelegram = $this->createMock(Telegram::class);

        $channel = new TelegramChannel([
            'token' => 'test-token',
        ], $mockTelegram);

        $notifiable = new TestRoutableEntity('123456');
        $notification = new TestConditionalNotification();

        $result = $channel->send($notifiable, $notification);

        $this->assertNull($result);
    }
}
