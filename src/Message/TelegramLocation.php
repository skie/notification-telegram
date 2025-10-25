<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Message;

use Cake\TelegramNotification\Trait\SharedLogicTrait;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Telegram Location Message
 *
 * Share location coordinates.
 */
class TelegramLocation
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
     * @param string|float $latitude Latitude
     * @param string|float $longitude Longitude
     */
    public function __construct(float|string $latitude = '', float|string $longitude = '')
    {
        $this->latitude($latitude);
        $this->longitude($longitude);
    }

    /**
     * Create new instance
     *
     * @param string|float $latitude Latitude
     * @param string|float $longitude Longitude
     * @return static
     */
    public static function create(float|string $latitude = '', float|string $longitude = ''): static
    {
        return new static($latitude, $longitude); // @phpstan-ignore-line
    }

    /**
     * Set latitude
     *
     * @param string|float $latitude Latitude
     * @return static
     */
    public function latitude(float|string $latitude): static
    {
        $this->payload['latitude'] = $latitude;

        return $this;
    }

    /**
     * Set longitude
     *
     * @param string|float $longitude Longitude
     * @return static
     */
    public function longitude(float|string $longitude): static
    {
        $this->payload['longitude'] = $longitude;

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
     * Send the location
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function send(): mixed
    {
        return Request::sendLocation($this->toArray());
    }
}
