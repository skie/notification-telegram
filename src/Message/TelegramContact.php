<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Message;

use Cake\TelegramNotification\Trait\SharedLogicTrait;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Telegram Contact Message
 *
 * Share contact information.
 */
class TelegramContact
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
     * @param string $phoneNumber Phone number
     */
    public function __construct(string $phoneNumber = '')
    {
        $this->phoneNumber($phoneNumber);
    }

    /**
     * Create new instance
     *
     * @param string $phoneNumber Phone number
     * @return static
     */
    public static function create(string $phoneNumber = ''): static
    {
        return new static($phoneNumber); // @phpstan-ignore-line
    }

    /**
     * Set phone number
     *
     * @param string $phoneNumber Phone number
     * @return static
     */
    public function phoneNumber(string $phoneNumber): static
    {
        $this->payload['phone_number'] = $phoneNumber;

        return $this;
    }

    /**
     * Set first name
     *
     * @param string $firstName First name
     * @return static
     */
    public function firstName(string $firstName): static
    {
        $this->payload['first_name'] = $firstName;

        return $this;
    }

    /**
     * Set last name
     *
     * @param string $lastName Last name
     * @return static
     */
    public function lastName(string $lastName): static
    {
        $this->payload['last_name'] = $lastName;

        return $this;
    }

    /**
     * Set vCard
     *
     * @param string $vCard vCard data
     * @return static
     */
    public function vCard(string $vCard): static
    {
        $this->payload['vcard'] = $vCard;

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
     * Send the contact
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function send(): mixed
    {
        return Request::sendContact($this->toArray());
    }
}
