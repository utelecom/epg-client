<?php

namespace EpgClient\Tests\Integration;

use EpgClient\Context\Channel;
use EpgClient\Context\ChannelImage;
use EpgClient\Tests\CustomApiTestCase;

/**
 * @package Tests\Integration
 * @group Integration
 */
class ChannelResourceTest extends CustomApiTestCase
{
    public function testGetChannels()
    {
        $content = $this->client->getChannelResource()
            ->get()
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(Channel::class, $content);

        return current($content);
    }

    public function testCreateChannel()
    {
        $context = $this->client->contextFactory()->createChannel();
        $context->externalId = 'phpUnit';
        $context->setTitle('phpUnit', 'en');

        /** @var Channel $content */
        $content = $this->client->getChannelResource()
            ->post($context)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Channel::class, $content);
        $this->assertEquals('phpUnit', $content->externalId);
        $this->assertEquals('phpUnit', $content->__get('translations')['en']['title']);

        return $content;
    }

    /**
     * @depends testCreateChannel
     * @param Channel $channel
     */
    public function testGetChannel(Channel $channel)
    {
        /** @var Channel $content */
        $content = $this->client->getChannelResource()
            ->get($channel->getLocation())
            ->withGroup('translations')
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Channel::class, $content);
        $this->assertEquals('phpUnit', $content->externalId);
        $this->assertEquals('phpUnit', $content->__get('translations')['en']['title']);
    }

    /**
     * @depends testCreateChannel
     * @param Channel $channel
     */
    public function testEditChannel(Channel $channel)
    {
        $channel->setTitle('phpUnit New', 'en');
        $channel->setTitle('phpUnit New', 'uk');

        /** @var Channel $content */
        $content = $this->client->getChannelResource()
            ->put($channel)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Channel::class, $content);
        $this->assertEquals('phpUnit', $content->externalId);
        $this->assertEquals('phpUnit New', $content->__get('translations')['en']['title']);
        $this->assertEquals('phpUnit New', $content->__get('translations')['uk']['title']);
    }

    /**
     * @depends testCreateChannel
     * @param Channel $channel
     */
    public function testAddImageToChannel(Channel $channel)
    {
        $image = $this->client->contextFactory()->createChannelImage();
        $image->uri = 'http://localhost/phpunit.png';

        /** @var ChannelImage $content */
        $content = $this->client->getChannelImageResource()
            ->addImageToChannel($image, $channel)
            ->exec()
            ->getSingleResult();
        $this->assertApiResponseSingleResult(ChannelImage::class, $content);
        $this->assertEquals($channel->getLocation(), $content->channel);

        // Две одинаковых картинки добавить нельзя
        $this->expectException(\RuntimeException::class);
        $this->client->getChannelImageResource()
            ->addImageToChannel($image, $channel)
            ->exec();
    }

    /**
     * @depends testCreateChannel
     * @param Channel $channel
     */
    public function testGetImagesByChannel(Channel $channel)
    {
        $content = $this->client->getChannelResource()
            ->getImages($channel->getLocation())
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(ChannelImage::class, $content);
    }

    /**
     * @depends testCreateChannel
     * @param Channel $channel
     */
    public function testDeleteChannel(Channel $channel)
    {
        /** @var Channel $content */
        $content = $this->client->getChannelResource()
            ->delete($channel)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Channel::class, $content);
    }

}
