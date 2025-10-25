<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Notification\AnonymousNotifiable;
use Cake\Notification\Channel\ChannelInterface;
use Cake\Notification\Exception\CouldNotSendNotification;
use Cake\Notification\Notification;
use Cake\TelegramNotification\Message\TelegramMessage;
use Exception;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Telegram Channel
 *
 * Sends notifications via Telegram Bot API using longman/telegram-bot library.
 */
class TelegramChannel implements ChannelInterface
{
    /**
     * Telegram bot instance
     *
     * @var \Longman\TelegramBot\Telegram
     */
    protected Telegram $telegram;

    /**
     * Channel configuration
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Constructor
     *
     * @param array<string, mixed> $config Channel configuration
     * @param \Longman\TelegramBot\Telegram|null $telegram Optional Telegram instance for testing
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function __construct(array $config = [], ?Telegram $telegram = null)
    {
        $this->config = $config;

        if (empty($this->config['token'])) {
            throw CouldNotSendNotification::missingCredentials('telegram', 'token');
        }

        $this->telegram = $telegram ?? new Telegram(
            $this->config['token'],
            $this->config['username'] ?? 'bot',
        );

        Request::initialize($this->telegram);
    }

    /**
     * Send the notification
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable The notifiable entity
     * @param \Cake\Notification\Notification $notification The notification to send
     * @return mixed Response from Telegram API
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function send(EntityInterface|AnonymousNotifiable $notifiable, Notification $notification): mixed
    {
        $message = $notification->toTelegram($notifiable);

        if ($message === null) {
            return null;
        }

        if (is_string($message)) {
            $message = TelegramMessage::create($message);
        }

        if (!$message->canSend()) {
            return null;
        }

        $chatId = $this->getChatId($message, $notifiable, $notification);

        if ($chatId === null) {
            return null;
        }

        $message->to($chatId);

        if ($message->hasToken()) {
            $tempTelegram = new Telegram($message->token, $this->config['username'] ?? 'bot');
            Request::initialize($tempTelegram);
            $message->setTelegram($tempTelegram);
        } else {
            $message->setTelegram($this->telegram);
        }

        try {
            $response = $message->send();

            return $this->parseResponse($response);
        } catch (Exception $e) {
            if ($message->exceptionHandler) {
                ($message->exceptionHandler)([
                    'to' => $chatId,
                    'request' => $message->toArray(),
                    'exception' => $e,
                ]);
            }

            throw CouldNotSendNotification::serviceRespondedWithError(
                'telegram',
                $e->getMessage(),
                "Failed to send Telegram notification: {$e->getMessage()}",
            );
        } finally {
            Request::initialize($this->telegram);
        }
    }

    /**
     * Parse Telegram API response
     *
     * @param mixed $response API response
     * @return mixed
     */
    protected function parseResponse(mixed $response): mixed
    {
        if ($response === null) {
            return null;
        }

        if (is_array($response)) {
            return $response;
        }

        if (is_object($response) && method_exists($response, 'getResult')) {
            return $response->getResult();
        }

        return $response;
    }

    /**
     * Get chat ID from message or notifiable
     *
     * @param mixed $message Message object
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable Notifiable
     * @param \Cake\Notification\Notification $notification Notification
     * @return string|null
     */
    protected function getChatId(mixed $message, EntityInterface|AnonymousNotifiable $notifiable, Notification $notification): ?string
    {
        if (is_object($message) && method_exists($message, 'getPayloadValue')) {
            $chatId = $message->getPayloadValue('chat_id');
            if ($chatId) {
                return (string)$chatId;
            }
        }

        return $this->getChatIdFromNotifiable($notifiable, $notification);
    }

    /**
     * Get chat ID from notifiable
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable Notifiable entity
     * @param \Cake\Notification\Notification $notification Notification instance
     * @return string|null
     */
    protected function getChatIdFromNotifiable(
        EntityInterface|AnonymousNotifiable $notifiable,
        Notification $notification,
    ): ?string {
        if ($notifiable instanceof AnonymousNotifiable) {
            return $notifiable->routeNotificationFor('telegram', $notification);
        }

        if (method_exists($notifiable, 'routeNotificationForTelegram')) {
            return $notifiable->routeNotificationForTelegram($notification);
        }

        if (isset($notifiable->telegram_chat_id)) {
            return $notifiable->telegram_chat_id;
        }

        return null;
    }
}
