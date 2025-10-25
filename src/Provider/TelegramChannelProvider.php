<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Provider;

use Cake\Core\Configure;
use Cake\Notification\Extension\ChannelProviderInterface;
use Cake\Notification\Registry\ChannelRegistry;
use Cake\TelegramNotification\Channel\TelegramChannel;

/**
 * Telegram Channel Provider
 *
 * Registers the Telegram channel with the notification system.
 */
class TelegramChannelProvider implements ChannelProviderInterface
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return ['telegram'];
    }

    /**
     * @inheritDoc
     */
    public function register(ChannelRegistry $registry): void
    {
        $config = array_merge(
            $this->getDefaultConfig(),
            (array)Configure::read('Notification.channels.telegram', []),
        );

        $registry->load('telegram', [
            'className' => TelegramChannel::class,
        ] + $config);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultConfig(): array
    {
        $token = getenv('TELEGRAM_BOT_TOKEN');

        return [
            'token' => $token !== false ? $token : null,
            'timeout' => 30,
        ];
    }
}
