<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Message;

use Cake\TelegramNotification\Trait\SharedLogicTrait;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Telegram Poll Message
 *
 * Create and send polls.
 */
class TelegramPoll
{
    use SharedLogicTrait;

    /**
     * Telegram instance
     *
     * @var \Longman\TelegramBot\Telegram|null
     */
    protected ?Telegram $telegram = null;

    /**
     * Constructor
     *
     * @param string $question Poll question
     */
    public function __construct(string $question = '')
    {
        $this->question($question);
    }

    /**
     * Create new instance
     *
     * @param string $question Poll question
     * @return static
     */
    public static function create(string $question = ''): static
    {
        return new static($question); // @phpstan-ignore-line
    }

    /**
     * Set poll question
     *
     * @param string $question Question
     * @return static
     */
    public function question(string $question): static
    {
        $this->payload['question'] = $question;

        return $this;
    }

    /**
     * Set poll choices
     *
     * @param array<string> $choices Answer options
     * @return static
     */
    public function choices(array $choices): static
    {
        $this->payload['options'] = json_encode($choices);

        return $this;
    }

    /**
     * Set Telegram instance
     *
     * @param \Longman\TelegramBot\Telegram $telegram Telegram instance
     * @return static
     */
    public function setTelegram(Telegram $telegram): static
    {
        $this->telegram = $telegram;

        return $this;
    }

    /**
     * Send the poll
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function send(): mixed
    {
        return Request::sendPoll($this->toArray());
    }
}
