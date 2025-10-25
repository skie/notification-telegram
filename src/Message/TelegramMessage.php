<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Message;

use Cake\TelegramNotification\Enum\ParseMode;
use Cake\TelegramNotification\Trait\SharedLogicTrait;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Telegram Message
 *
 * Fluent API for building Telegram Bot API messages with full feature support.
 */
class TelegramMessage
{
    use SharedLogicTrait;

    /**
     * Default chunk size for long messages
     */
    private const DEFAULT_CHUNK_SIZE = 4096;

    /**
     * Chunk separator
     */
    private const CHUNK_SEPARATOR = '%#TGMSG#%';

    /**
     * Chunk size for splitting messages
     *
     * @var int
     */
    public int $chunkSize = 0;

    /**
     * Telegram instance
     *
     * @var \Longman\TelegramBot\Telegram|null
     */
    protected ?Telegram $telegram = null;

    /**
     * Constructor
     *
     * @param string $content Message content
     * @param int $chunkSize Chunk size for long messages
     */
    public function __construct(string $content = '', int $chunkSize = 0)
    {
        $this->content($content);
        $this->chunkSize = $chunkSize;
        $this->parseMode(ParseMode::Markdown);
    }

    /**
     * Create new message instance
     *
     * @param string $content Message content
     * @return static
     */
    public static function create(string $content = ''): static
    {
        return new static($content); // @phpstan-ignore-line
    }

    /**
     * Escape markdown special characters
     *
     * @param string $content Content to escape
     * @return string|null
     */
    public static function escapeMarkdown(string $content): ?string
    {
        return preg_replace_callback(
            '/[_*[\]()~`>#\+\-=|{}.!]/',
            fn($matches): string => "\\{$matches[0]}",
            $content,
        );
    }

    /**
     * Set message content
     *
     * @param string $content Message content
     * @param int|null $limit Optional chunk limit
     * @return static
     */
    public function content(string $content, ?int $limit = null): static
    {
        $this->payload['text'] = $content;
        if ($limit !== null) {
            $this->chunkSize = $limit;
        }

        return $this;
    }

    /**
     * Add a line to message
     *
     * @param string $content Line content
     * @return static
     */
    public function line(string $content): static
    {
        if (!isset($this->payload['text'])) {
            $this->payload['text'] = '';
        } elseif ($this->payload['text'] !== '' && !str_ends_with($this->payload['text'], "\n")) {
            $this->payload['text'] .= "\n";
        }
        $this->payload['text'] .= $content . "\n";

        return $this;
    }

    /**
     * Add a line conditionally
     *
     * @param bool $condition Condition
     * @param string $line Line content
     * @return static
     */
    public function lineIf(bool $condition, string $line): static
    {
        return $condition ? $this->line($line) : $this;
    }

    /**
     * Add escaped line for Markdown
     *
     * @param string $content Content to escape and add
     * @return static
     */
    public function escapedLine(string $content): static
    {
        $content = str_replace('\\', '\\\\', $content);
        $escapedContent = self::escapeMarkdown($content) ?? $content;

        return $this->line($escapedContent);
    }

    /**
     * Set parse mode to Markdown
     *
     * @return static
     */
    public function markdown(): static
    {
        return $this->parseMode(ParseMode::Markdown);
    }

    /**
     * Set parse mode to MarkdownV2
     *
     * @return static
     */
    public function markdownV2(): static
    {
        return $this->parseMode(ParseMode::MarkdownV2);
    }

    /**
     * Set parse mode to HTML
     *
     * @return static
     */
    public function html(): static
    {
        return $this->parseMode(ParseMode::HTML);
    }

    /**
     * Disable web page preview
     *
     * @return static
     */
    public function disablePreview(): static
    {
        $this->payload['disable_web_page_preview'] = true;

        return $this;
    }

    /**
     * Protect content from forwarding
     *
     * @return static
     */
    public function protectContent(): static
    {
        $this->payload['protect_content'] = true;

        return $this;
    }

    /**
     * Enable message chunking
     *
     * @param int $limit Chunk size limit
     * @return static
     */
    public function chunk(int $limit = self::DEFAULT_CHUNK_SIZE): static
    {
        $this->chunkSize = $limit;

        return $this;
    }

    /**
     * Check if should chunk
     *
     * @return bool
     */
    public function shouldChunk(): bool
    {
        return $this->chunkSize > 0;
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
     * Send the message
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function send(): mixed
    {
        if ($this->shouldChunk()) {
            return $this->sendChunkedMessage($this->toArray());
        }

        return Request::sendMessage($this->toArray());
    }

    /**
     * Send chunked messages
     *
     * @param array<string, mixed> $params Message parameters
     * @return array<int, mixed>
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function sendChunkedMessage(array $params): array
    {
        $replyMarkup = $this->getPayloadValue('reply_markup');

        if ($replyMarkup) {
            unset($params['reply_markup']);
        }

        $messages = $this->chunkStrings($params['text'], $this->chunkSize);
        $lastIndex = count($messages) - 1;
        $results = [];

        foreach ($messages as $index => $text) {
            $payload = array_merge($params, ['text' => $text]);

            if ($index === $lastIndex && $replyMarkup !== null) {
                $payload['reply_markup'] = $replyMarkup;
            }

            $response = Request::sendMessage($payload);
            $results[] = $response;

            if ($index < $lastIndex) {
                sleep(1);
            }
        }

        return $results;
    }

    /**
     * Chunk string into smaller parts
     *
     * @param string $value Value to chunk
     * @param int $limit Chunk size
     * @return array<int, string>
     */
    private function chunkStrings(string $value, int $limit = self::DEFAULT_CHUNK_SIZE): array
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return [$value];
        }

        $limit = min($limit, self::DEFAULT_CHUNK_SIZE);
        $output = explode(self::CHUNK_SEPARATOR, wordwrap($value, $limit, self::CHUNK_SEPARATOR));

        return count($output) <= 1
            ? mb_str_split($value, max(1, $limit), 'UTF-8')
            : $output;
    }
}
