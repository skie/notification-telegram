<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Message;

use Cake\TelegramNotification\Trait\SharedLogicTrait;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Telegram Venue Message
 *
 * Share venue information with location.
 */
class TelegramVenue
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
     * @param string $title Venue title
     * @param string $address Venue address
     */
    public function __construct(
        float|string $latitude = '',
        float|string $longitude = '',
        string $title = '',
        string $address = '',
    ) {
        $this->latitude($latitude);
        $this->longitude($longitude);
        $this->title($title);
        $this->address($address);
    }

    /**
     * Create new instance
     *
     * @param string|float $latitude Latitude
     * @param string|float $longitude Longitude
     * @param string $title Venue title
     * @param string $address Venue address
     * @return static
     */
    public static function create(
        float|string $latitude = '',
        float|string $longitude = '',
        string $title = '',
        string $address = '',
    ): static {
        return new static($latitude, $longitude, $title, $address); // @phpstan-ignore-line
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
     * Set venue title
     *
     * @param string $title Title
     * @return static
     */
    public function title(string $title): static
    {
        $this->payload['title'] = $title;

        return $this;
    }

    /**
     * Set venue address
     *
     * @param string $address Address
     * @return static
     */
    public function address(string $address): static
    {
        $this->payload['address'] = $address;

        return $this;
    }

    /**
     * Set Foursquare ID
     *
     * @param string $foursquareId Foursquare ID
     * @return static
     */
    public function foursquareId(string $foursquareId): static
    {
        $this->payload['foursquare_id'] = $foursquareId;

        return $this;
    }

    /**
     * Set Foursquare type
     *
     * @param string $foursquareType Foursquare type
     * @return static
     */
    public function foursquareType(string $foursquareType): static
    {
        $this->payload['foursquare_type'] = $foursquareType;

        return $this;
    }

    /**
     * Set Google Place ID
     *
     * @param string $googlePlaceId Google Place ID
     * @return static
     */
    public function googlePlaceId(string $googlePlaceId): static
    {
        $this->payload['google_place_id'] = $googlePlaceId;

        return $this;
    }

    /**
     * Set Google Place type
     *
     * @param string $googlePlaceType Google Place type
     * @return static
     */
    public function googlePlaceType(string $googlePlaceType): static
    {
        $this->payload['google_place_type'] = $googlePlaceType;

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
     * Send the venue
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function send(): mixed
    {
        return Request::sendVenue($this->toArray());
    }
}
