<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Enum;

/**
 * Parse Mode Enum
 *
 * Telegram message formatting modes.
 */
enum ParseMode: string
{
    case Markdown = 'Markdown';
    case MarkdownV2 = 'MarkdownV2';
    case HTML = 'HTML';
}
