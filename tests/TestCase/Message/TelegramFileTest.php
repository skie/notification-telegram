<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Message;

use Cake\TelegramNotification\Enum\FileType;
use Cake\TelegramNotification\Message\TelegramFile;
use Cake\TestSuite\TestCase;

/**
 * TelegramFile Test
 */
class TelegramFileTest extends TestCase
{
    /**
     * Test create with caption
     *
     * @return void
     */
    public function testCreateWithCaption(): void
    {
        $file = new TelegramFile('Test caption');

        $this->assertEquals('Test caption', $file->getPayloadValue('caption'));
    }

    /**
     * Test photo method
     *
     * @return void
     */
    public function testPhotoMethod(): void
    {
        $file = TelegramFile::create()->photo('https://example.com/image.jpg');

        $this->assertEquals(FileType::Photo, $file->type);
        $this->assertEquals('https://example.com/image.jpg', $file->getPayloadValue('photo'));
    }

    /**
     * Test document method
     *
     * @return void
     */
    public function testDocumentMethod(): void
    {
        $file = TelegramFile::create()->document('https://example.com/file.pdf');

        $this->assertEquals(FileType::Document, $file->type);
        $this->assertEquals('https://example.com/file.pdf', $file->getPayloadValue('document'));
    }

    /**
     * Test audio method
     *
     * @return void
     */
    public function testAudioMethod(): void
    {
        $file = TelegramFile::create()->audio('https://example.com/audio.mp3');

        $this->assertEquals(FileType::Audio, $file->type);
    }

    /**
     * Test video method
     *
     * @return void
     */
    public function testVideoMethod(): void
    {
        $file = TelegramFile::create()->video('https://example.com/video.mp4');

        $this->assertEquals(FileType::Video, $file->type);
    }

    /**
     * Test animation method
     *
     * @return void
     */
    public function testAnimationMethod(): void
    {
        $file = TelegramFile::create()->animation('https://example.com/anim.gif');

        $this->assertEquals(FileType::Animation, $file->type);
    }

    /**
     * Test voice method
     *
     * @return void
     */
    public function testVoiceMethod(): void
    {
        $file = TelegramFile::create()->voice('https://example.com/voice.ogg');

        $this->assertEquals(FileType::Voice, $file->type);
    }

    /**
     * Test video note method
     *
     * @return void
     */
    public function testVideoNoteMethod(): void
    {
        $file = TelegramFile::create()->videoNote('https://example.com/note.mp4');

        $this->assertEquals(FileType::VideoNote, $file->type);
    }

    /**
     * Test sticker method
     *
     * @return void
     */
    public function testStickerMethod(): void
    {
        $file = TelegramFile::create()->sticker('https://example.com/sticker.webp');

        $this->assertEquals(FileType::Sticker, $file->type);
    }

    /**
     * Test caption is removed for unsupported types
     *
     * @return void
     */
    public function testCaptionRemovedForUnsupportedTypes(): void
    {
        $videoNote = TelegramFile::create('Caption')->videoNote('https://example.com/note.mp4');
        $this->assertArrayNotHasKey('caption', $videoNote->toArray());

        $sticker = TelegramFile::create('Caption')->sticker('https://example.com/sticker.webp');
        $this->assertArrayNotHasKey('caption', $sticker->toArray());
    }

    /**
     * Test caption maintained for supported types
     *
     * @return void
     */
    public function testCaptionMaintainedForSupportedTypes(): void
    {
        $photo = TelegramFile::create('Photo caption')->photo('https://example.com/image.jpg');
        $this->assertEquals('Photo caption', $photo->getPayloadValue('caption'));

        $document = TelegramFile::create('Doc caption')->document('https://example.com/file.pdf');
        $this->assertEquals('Doc caption', $document->getPayloadValue('caption'));
    }

    /**
     * Test hasFile method
     *
     * @return void
     */
    public function testHasFile(): void
    {
        $file = TelegramFile::create();
        $this->assertFalse($file->hasFile());
    }
}
