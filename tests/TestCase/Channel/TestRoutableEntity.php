<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Channel;

use Cake\ORM\Entity;

/**
 * Test Routable Entity
 */
class TestRoutableEntity extends Entity
{
    /**
     * Constructor
     *
     * @param string|null $chatId Telegram chat ID
     */
    public function __construct(?string $chatId)
    {
        parent::__construct([
            'telegram_chat_id' => $chatId,
        ]);
    }
}
