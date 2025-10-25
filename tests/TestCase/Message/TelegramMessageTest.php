<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Message;

use Cake\TelegramNotification\Message\TelegramMessage;
use Cake\TestSuite\TestCase;

/**
 * TelegramMessage Test
 */
class TelegramMessageTest extends TestCase
{
    /**
     * Test create with content
     *
     * @return void
     */
    public function testCreateWithContent(): void
    {
        $message = new TelegramMessage('Test content');

        $this->assertEquals('Test content', $message->getPayloadValue('text'));
        $this->assertEquals('Markdown', $message->getPayloadValue('parse_mode'));
    }

    /**
     * Test static create
     *
     * @return void
     */
    public function testStaticCreate(): void
    {
        $message = TelegramMessage::create('Static content');

        $this->assertEquals('Static content', $message->getPayloadValue('text'));
    }

    /**
     * Test content method
     *
     * @return void
     */
    public function testContentMethod(): void
    {
        $message = TelegramMessage::create()->content('New content');

        $this->assertEquals('New content', $message->getPayloadValue('text'));
    }

    /**
     * Test line method
     *
     * @return void
     */
    public function testLineMethod(): void
    {
        $message = TelegramMessage::create()
            ->content('First')
            ->line('Second')
            ->line('Third');

        $this->assertEquals("First\nSecond\nThird\n", $message->getPayloadValue('text'));
    }

    /**
     * Test lineIf method
     *
     * @return void
     */
    public function testLineIfMethod(): void
    {
        $message = TelegramMessage::create()
            ->content('Start')
            ->lineIf(true, 'Included')
            ->lineIf(false, 'Excluded')
            ->lineIf(true, 'Also included');

        $expected = "Start\nIncluded\nAlso included\n";
        $this->assertEquals($expected, $message->getPayloadValue('text'));
    }

    /**
     * Test escapedLine method
     *
     * @return void
     */
    public function testEscapedLineMethod(): void
    {
        $message = TelegramMessage::create()
            ->escapedLine('Text_with_underscores');

        $this->assertStringContainsString('Text\\_with\\_underscores', $message->getPayloadValue('text'));
    }

    /**
     * Test escapeMarkdown static method
     *
     * @return void
     */
    public function testEscapeMarkdown(): void
    {
        $escaped = TelegramMessage::escapeMarkdown('_*[]()~`>#+-=|{}.!');

        $this->assertEquals('\\_\\*\\[\\]\\(\\)\\~\\`\\>\\#\\+\\-\\=\\|\\{\\}\\.\\!', $escaped);
    }

    /**
     * Test parse modes
     *
     * @return void
     */
    public function testParseModes(): void
    {
        $messageMarkdown = TelegramMessage::create()->markdown();
        $this->assertEquals('Markdown', $messageMarkdown->getPayloadValue('parse_mode'));

        $messageMarkdownV2 = TelegramMessage::create()->markdownV2();
        $this->assertEquals('MarkdownV2', $messageMarkdownV2->getPayloadValue('parse_mode'));

        $messageHtml = TelegramMessage::create()->html();
        $this->assertEquals('HTML', $messageHtml->getPayloadValue('parse_mode'));

        $messageNormal = TelegramMessage::create()->normal();
        $this->assertNull($messageNormal->getPayloadValue('parse_mode'));
    }

    /**
     * Test disable preview
     *
     * @return void
     */
    public function testDisablePreview(): void
    {
        $message = TelegramMessage::create()->disablePreview();

        $this->assertTrue($message->getPayloadValue('disable_web_page_preview'));
    }

    /**
     * Test disable notification
     *
     * @return void
     */
    public function testDisableNotification(): void
    {
        $message = TelegramMessage::create()->disableNotification();

        $this->assertTrue($message->getPayloadValue('disable_notification'));
    }

    /**
     * Test protect content
     *
     * @return void
     */
    public function testProtectContent(): void
    {
        $message = TelegramMessage::create()->protectContent();

        $this->assertTrue($message->getPayloadValue('protect_content'));
    }

    /**
     * Test inline buttons
     *
     * @return void
     */
    public function testInlineButtons(): void
    {
        $message = TelegramMessage::create()
            ->button('Button 1', 'https://example.com/1')
            ->button('Button 2', 'https://example.com/2');

        $replyMarkup = json_decode($message->getPayloadValue('reply_markup'), true);

        $this->assertCount(2, $replyMarkup['inline_keyboard'][0]);
        $this->assertEquals('Button 1', $replyMarkup['inline_keyboard'][0][0]['text']);
        $this->assertEquals('https://example.com/1', $replyMarkup['inline_keyboard'][0][0]['url']);
    }

    /**
     * Test button with callback
     *
     * @return void
     */
    public function testButtonWithCallback(): void
    {
        $message = TelegramMessage::create()
            ->buttonWithCallback('Click Me', 'callback_data');

        $replyMarkup = json_decode($message->getPayloadValue('reply_markup'), true);

        $this->assertEquals('Click Me', $replyMarkup['inline_keyboard'][0][0]['text']);
        $this->assertEquals('callback_data', $replyMarkup['inline_keyboard'][0][0]['callback_data']);
    }

    /**
     * Test button with web app
     *
     * @return void
     */
    public function testButtonWithWebApp(): void
    {
        $message = TelegramMessage::create()
            ->buttonWithWebApp('Open App', 'https://app.example.com');

        $replyMarkup = json_decode($message->getPayloadValue('reply_markup'), true);

        $this->assertEquals('Open App', $replyMarkup['inline_keyboard'][0][0]['text']);
        $this->assertEquals('https://app.example.com', $replyMarkup['inline_keyboard'][0][0]['web_app']['url']);
    }

    /**
     * Test keyboard buttons
     *
     * @return void
     */
    public function testKeyboardButtons(): void
    {
        $message = TelegramMessage::create()
            ->keyboard('Button 1')
            ->keyboard('Button 2', 2, true, false);

        $replyMarkup = json_decode($message->getPayloadValue('reply_markup'), true);

        $this->assertArrayHasKey('keyboard', $replyMarkup);
        $this->assertTrue($replyMarkup['one_time_keyboard']);
        $this->assertTrue($replyMarkup['resize_keyboard']);
    }

    /**
     * Test token override
     *
     * @return void
     */
    public function testTokenOverride(): void
    {
        $message = TelegramMessage::create()->token('custom-token');

        $this->assertTrue($message->hasToken());
        $this->assertEquals('custom-token', $message->token);
    }

    /**
     * Test options method
     *
     * @return void
     */
    public function testOptionsMethod(): void
    {
        $message = TelegramMessage::create()->options(['custom' => 'value']);

        $this->assertEquals('value', $message->getPayloadValue('custom'));
    }

    /**
     * Test sendWhen method
     *
     * @return void
     */
    public function testSendWhen(): void
    {
        $messageTrue = TelegramMessage::create()->sendWhen(true);
        $this->assertTrue($messageTrue->canSend());

        $messageFalse = TelegramMessage::create()->sendWhen(false);
        $this->assertFalse($messageFalse->canSend());
    }

    /**
     * Test toNotGiven method
     *
     * @return void
     */
    public function testToNotGiven(): void
    {
        $message = TelegramMessage::create();
        $this->assertTrue($message->toNotGiven());

        $message->to(12345);
        $this->assertFalse($message->toNotGiven());
    }

    /**
     * Test chunking
     *
     * @return void
     */
    public function testChunking(): void
    {
        $message = TelegramMessage::create()->chunk(100);

        $this->assertTrue($message->shouldChunk());
        $this->assertEquals(100, $message->chunkSize);
    }

    /**
     * Test toArray
     *
     * @return void
     */
    public function testToArray(): void
    {
        $message = TelegramMessage::create('Test')
            ->to(12345)
            ->disablePreview()
            ->button('Click', 'https://example.com');

        $array = $message->toArray();

        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('chat_id', $array);
        $this->assertArrayHasKey('disable_web_page_preview', $array);
        $this->assertArrayHasKey('reply_markup', $array);
    }
}
