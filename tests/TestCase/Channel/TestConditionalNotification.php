<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Notification\AnonymousNotifiable;
use Cake\Notification\Notification;
use Cake\TelegramNotification\Message\TelegramMessage;

/**
 * Test Notification with sendWhen condition
 */
class TestConditionalNotification extends Notification
{
    /**
     * @inheritDoc
     */
    public function via(EntityInterface|AnonymousNotifiable $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * @inheritDoc
     */
    public function toTelegram(EntityInterface|AnonymousNotifiable $notifiable): TelegramMessage
    {
        return TelegramMessage::create('Test')
            ->sendWhen(false);
    }
}
