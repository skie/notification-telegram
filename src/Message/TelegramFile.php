<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Message;

use Cake\TelegramNotification\Enum\FileType;
use Cake\TelegramNotification\Enum\ParseMode;
use Cake\TelegramNotification\Trait\SharedLogicTrait;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Telegram File Message
 *
 * Handles file-based notifications (photos, documents, audio, video, etc.)
 */
class TelegramFile
{
    use SharedLogicTrait;

    /**
     * File type
     *
     * @var \Cake\TelegramNotification\Enum\FileType
     */
    public FileType $type = FileType::Document;

    /**
     * File types that don't support captions
     *
     * @var array<\Cake\TelegramNotification\Enum\FileType>
     */
    protected array $captionUnsupportedTypes = [
        FileType::VideoNote,
        FileType::Sticker,
    ];

    /**
     * Telegram instance
     *
     * @var \Longman\TelegramBot\Telegram|null
     */
    protected ?Telegram $telegram = null;

    /**
     * Constructor
     *
     * @param string $content Caption content
     */
    public function __construct(string $content = '')
    {
        $this->content($content);
        $this->parseMode(ParseMode::Markdown);
    }

    /**
     * Create new instance
     *
     * @param string $content Caption content
     * @return static
     */
    public static function create(string $content = ''): static
    {
        return new static($content); // @phpstan-ignore-line
    }

    /**
     * Set caption content
     *
     * @param string $content Caption
     * @return static
     */
    public function content(string $content): static
    {
        $this->payload['caption'] = $content;

        return $this;
    }

    /**
     * Attach file
     *
     * @param mixed $file File URL, file ID, or resource
     * @param \Cake\TelegramNotification\Enum\FileType|string $type File type
     * @param string|null $filename Optional filename
     * @return static
     */
    public function file(mixed $file, FileType|string $type, ?string $filename = null): static
    {
        $this->type = is_string($type) ? FileType::tryFrom($type) ?? FileType::Document : $type;
        $typeValue = $this->type->value;

        if (is_string($file) && !is_file($file) && $filename === null) {
            $this->payload[$typeValue] = $file;

            return $this;
        }

        $this->payload['file'] = [
            'name' => $typeValue,
            'contents' => $file,
        ];

        if ($filename !== null) {
            $this->payload['file']['filename'] = $filename;
        }

        return $this;
    }

    /**
     * Attach photo
     *
     * @param string $file Photo URL or file ID
     * @return static
     */
    public function photo(string $file): static
    {
        return $this->file($file, FileType::Photo);
    }

    /**
     * Attach audio
     *
     * @param string $file Audio URL or file ID
     * @return static
     */
    public function audio(string $file): static
    {
        return $this->file($file, FileType::Audio);
    }

    /**
     * Attach document
     *
     * @param string $file Document URL or file ID
     * @param string|null $filename Optional filename
     * @return static
     */
    public function document(string $file, ?string $filename = null): static
    {
        return $this->file($file, FileType::Document, $filename);
    }

    /**
     * Attach video
     *
     * @param string $file Video URL or file ID
     * @return static
     */
    public function video(string $file): static
    {
        return $this->file($file, FileType::Video);
    }

    /**
     * Attach animation
     *
     * @param string $file Animation URL or file ID
     * @return static
     */
    public function animation(string $file): static
    {
        return $this->file($file, FileType::Animation);
    }

    /**
     * Attach voice message
     *
     * @param string $file Voice URL or file ID
     * @return static
     */
    public function voice(string $file): static
    {
        return $this->file($file, FileType::Voice);
    }

    /**
     * Attach video note
     *
     * @param string $file Video note URL or file ID
     * @return static
     */
    public function videoNote(string $file): static
    {
        return $this->file($file, FileType::VideoNote);
    }

    /**
     * Attach sticker
     *
     * @param string $file Sticker URL or file ID
     * @return static
     */
    public function sticker(string $file): static
    {
        return $this->file($file, FileType::Sticker);
    }

    /**
     * Check if file is attached
     *
     * @return bool
     */
    public function hasFile(): bool
    {
        return isset($this->payload['file']);
    }

    /**
     * Check if type supports captions
     *
     * @return bool
     */
    protected function supportsCaptions(): bool
    {
        return !in_array($this->type, $this->captionUnsupportedTypes, true);
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = $this->payload;

        if (!$this->supportsCaptions() && isset($payload['caption'])) {
            unset($payload['caption']);
        }

        return $payload;
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
     * Send the file
     *
     * @return mixed
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function send(): mixed
    {
        $typeValue = str_replace('_', '', ucwords($this->type->value, '_'));
        $method = 'send' . $typeValue;

        return Request::$method($this->toArray());
    }
}
