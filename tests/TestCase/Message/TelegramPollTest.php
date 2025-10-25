<?php
declare(strict_types=1);

namespace Cake\TelegramNotification\Test\TestCase\Message;

use Cake\TelegramNotification\Message\TelegramPoll;
use Cake\TestSuite\TestCase;

/**
 * TelegramPoll Test
 */
class TelegramPollTest extends TestCase
{
    /**
     * Test create with question
     *
     * @return void
     */
    public function testCreateWithQuestion(): void
    {
        $poll = new TelegramPoll('Is this a test?');

        $this->assertEquals('Is this a test?', $poll->getPayloadValue('question'));
    }

    /**
     * Test static create
     *
     * @return void
     */
    public function testStaticCreate(): void
    {
        $poll = TelegramPoll::create('Static question?');

        $this->assertEquals('Static question?', $poll->getPayloadValue('question'));
    }

    /**
     * Test question method
     *
     * @return void
     */
    public function testQuestionMethod(): void
    {
        $poll = TelegramPoll::create()->question('New question?');

        $this->assertEquals('New question?', $poll->getPayloadValue('question'));
    }

    /**
     * Test choices method
     *
     * @return void
     */
    public function testChoicesMethod(): void
    {
        $poll = TelegramPoll::create()->choices(['Yes', 'No', 'Maybe']);

        $options = json_decode($poll->getPayloadValue('options'), true);
        $this->assertCount(3, $options);
        $this->assertEquals('Yes', $options[0]);
    }

    /**
     * Test complete poll
     *
     * @return void
     */
    public function testCompletePoll(): void
    {
        $poll = TelegramPoll::create('Question?')
            ->to(12345)
            ->choices(['A', 'B', 'C']);

        $array = $poll->toArray();

        $this->assertEquals(12345, $array['chat_id']);
        $this->assertEquals('Question?', $array['question']);
        $this->assertArrayHasKey('options', $array);
    }
}
