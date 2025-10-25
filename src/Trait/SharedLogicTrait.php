<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Trait;

use Cake\TelegramNotification\Enum\ParseMode;
use Closure;

/**
 * Shared Logic Trait
 *
 * Provides shared functionality for all Telegram message types.
 */
trait SharedLogicTrait
{
    /**
     * Bot token override
     *
     * @var string|null
     */
    public ?string $token = null;

    /**
     * Payload data
     *
     * @var array<string, mixed>
     */
    protected array $payload = [];

    /**
     * Inline keyboard buttons
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $buttons = [];

    /**
     * Regular keyboard buttons
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $keyboards = [];

    /**
     * Condition for sending
     *
     * @var bool|null
     */
    private ?bool $sendCondition = null;

    /**
     * Exception handler
     *
     * @var \Closure|null
     */
    public ?Closure $exceptionHandler = null;

    /**
     * Set recipient chat ID
     *
     * @param string|int $chatId Chat ID
     * @return static
     */
    public function to(int|string $chatId): static
    {
        $this->payload['chat_id'] = $chatId;

        return $this;
    }

    /**
     * Set parse mode
     *
     * @param \Cake\TelegramNotification\Enum\ParseMode|string $mode Parse mode
     * @return static
     */
    public function parseMode(ParseMode|string $mode): static
    {
        $this->payload['parse_mode'] = $mode instanceof ParseMode ? $mode->value : $mode;

        return $this;
    }

    /**
     * Unset parse mode
     *
     * @return static
     */
    public function normal(): static
    {
        unset($this->payload['parse_mode']);

        return $this;
    }

    /**
     * Add inline button with URL
     *
     * @param string $text Button text
     * @param string $url Button URL
     * @param int $columns Number of columns for button layout
     * @return static
     */
    public function button(string $text, string $url, int $columns = 2): static
    {
        $this->buttons[] = compact('text', 'url');

        return $this->updateInlineKeyboard($columns);
    }

    /**
     * Add inline button with callback data
     *
     * @param string $text Button text
     * @param string $callbackData Callback data
     * @param int $columns Number of columns
     * @return static
     */
    public function buttonWithCallback(string $text, string $callbackData, int $columns = 2): static
    {
        $this->buttons[] = [
            'text' => $text,
            'callback_data' => $callbackData,
        ];

        return $this->updateInlineKeyboard($columns);
    }

    /**
     * Add inline button with web app
     *
     * @param string $text Button text
     * @param string $url Web app URL
     * @param int $columns Number of columns
     * @return static
     */
    public function buttonWithWebApp(string $text, string $url, int $columns = 2): static
    {
        $this->buttons[] = [
            'text' => $text,
            'web_app' => ['url' => $url],
        ];

        return $this->updateInlineKeyboard($columns);
    }

    /**
     * Add keyboard button
     *
     * @param string $text Button text
     * @param int $columns Number of columns
     * @param bool $requestContact Request contact
     * @param bool $requestLocation Request location
     * @return static
     */
    public function keyboard(
        string $text,
        int $columns = 2,
        bool $requestContact = false,
        bool $requestLocation = false,
    ): static {
        $this->keyboards[] = [
            'text' => $text,
            'request_contact' => $requestContact,
            'request_location' => $requestLocation,
        ];

        $this->payload['reply_markup'] = json_encode([
            'keyboard' => array_chunk($this->keyboards, max(1, $columns)),
            'one_time_keyboard' => true,
            'resize_keyboard' => true,
        ]);

        return $this;
    }

    /**
     * Send message silently
     *
     * @param bool $disable Whether to disable notification
     * @return static
     */
    public function disableNotification(bool $disable = true): static
    {
        $this->payload['disable_notification'] = $disable;

        return $this;
    }

    /**
     * Set bot token override
     *
     * @param string $token Bot token
     * @return static
     */
    public function token(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Check if token is set
     *
     * @return bool
     */
    public function hasToken(): bool
    {
        return $this->token !== null;
    }

    /**
     * Set additional options
     *
     * @param array<string, mixed> $options Additional options
     * @return static
     */
    public function options(array $options): static
    {
        $this->payload = array_merge($this->payload, $options);

        return $this;
    }

    /**
     * Register error handler
     *
     * @param \Closure $callback Error handler callback
     * @return static
     */
    public function onError(Closure $callback): static
    {
        $this->exceptionHandler = $callback;

        return $this;
    }

    /**
     * Set condition for sending
     *
     * @param bool $condition Send condition
     * @return static
     */
    public function sendWhen(bool $condition): static
    {
        $this->sendCondition = $condition;

        return $this;
    }

    /**
     * Check if message can be sent
     *
     * @return bool
     */
    public function canSend(): bool
    {
        return $this->sendCondition ?? true;
    }

    /**
     * Check if chat ID is not given
     *
     * @return bool
     */
    public function toNotGiven(): bool
    {
        return !isset($this->payload['chat_id']);
    }

    /**
     * Get payload value
     *
     * @param string $key Payload key
     * @return mixed
     */
    public function getPayloadValue(string $key): mixed
    {
        return $this->payload[$key] ?? null;
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->payload;
    }

    /**
     * Update inline keyboard
     *
     * @param int $columns Number of columns
     * @return static
     */
    private function updateInlineKeyboard(int $columns): static
    {
        $this->payload['reply_markup'] = json_encode([
            'inline_keyboard' => array_chunk($this->buttons, max(1, $columns)),
        ]);

        return $this;
    }
}
