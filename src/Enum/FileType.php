<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Enum;

/**
 * File Type Enum
 *
 * Represents different file types supported by Telegram Bot API.
 */
enum FileType: string
{
    case Document = 'document';
    case Photo = 'photo';
    case Audio = 'audio';
    case Video = 'video';
    case Animation = 'animation';
    case Voice = 'voice';
    case VideoNote = 'video_note';
    case Sticker = 'sticker';

    /**
     * Get allowed file extensions
     *
     * @return array<string>
     */
    public function getAllowedExtensions(): array
    {
        return match ($this) {
            self::Document => [],
            self::Photo => ['jpg', 'jpeg', 'png', 'webp'],
            self::Audio => ['mp3', 'ogg', 'm4a'],
            self::Video => ['mp4', 'avi', 'mov', 'mkv'],
            self::Animation => ['gif', 'mp4'],
            self::Voice => ['ogg', 'mp3'],
            self::VideoNote => ['mp4'],
            self::Sticker => ['png', 'webp', 'tgs', 'webm'],
        };
    }

    /**
     * Check if extension is allowed
     *
     * @param string $extension File extension
     * @return bool
     */
    public function isExtensionAllowed(string $extension): bool
    {
        $extensions = $this->getAllowedExtensions();

        if ($this === self::Document || empty($extensions)) {
            return true;
        }

        return in_array(strtolower($extension), $extensions, true);
    }
}
