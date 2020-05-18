<?php

namespace EpgClient\Tests\Integration;

use EpgClient\Context\Category;
use EpgClient\Tests\CustomApiTestCase;

/**
 * @package Tests\Integration
 * @group Integration
 */
class CategoryResourceTest extends CustomApiTestCase
{
    public function testGetCategories()
    {
        $content = $this->client->getCategoryResource()
            ->get()
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(Category::class, $content);

        return current($content);
    }

    public function testCreateCategory()
    {
        $context = $this->client->contextFactory()->createCategory();
        $context->externalId = 'phpUnit Category';
        $context->setTitle('phpUnit Category', 'en');

        /** @var Category $content */
        $content = $this->client->getCategoryResource()
            ->post($context)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Category::class, $content);
        $this->assertEquals('phpUnit Category', $content->externalId);
        $this->assertEquals('phpUnit Category', $content->__get('translations')['en']['title']);

        return $content;
    }

    /**
     * @depends testCreateCategory
     * @param Category $category
     */
    public function testGetChannel(Category $category)
    {
        /** @var Category $content */
        $content = $this->client->getCategoryResource()
            ->get($category->getLocation())
            ->withGroup('translations')
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Category::class, $content);
        $this->assertEquals('phpUnit Category', $content->externalId);
        $this->assertEquals('phpUnit Category', $content->__get('translations')['en']['title']);
    }

    /**
     * @depends testCreateCategory
     * @param Category $category
     */
    public function testEditChannel(Category $category)
    {
        $category->setTitle('phpUnit Category New', 'en');
        $category->setTitle('phpUnit Category New', 'uk');

        /** @var Category $content */
        $content = $this->client->getCategoryResource()
            ->put($category)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Category::class, $content);
        $this->assertEquals('phpUnit Category', $content->externalId);
        $this->assertEquals('phpUnit Category New', $content->__get('translations')['en']['title']);
        $this->assertEquals('phpUnit Category New', $content->__get('translations')['uk']['title']);
    }

//    /**
//     * @depends testCreateChannel
//     * @param Channel $channel
//     */
//    public function testAddImageToChannel(Channel $channel)
//    {
//        $image = $this->client->contextFactory()->createChannelImage();
//        $image->uri = 'http://localhost/phpunit.png';
//
//        /** @var ChannelImage $content */
//        $content = $this->client->getChannelImageResource()
//            ->addImageToChannel($image, $channel)
//            ->exec()
//            ->getSingleResult();
//        $this->assertApiResponseSingleResult(ChannelImage::class, $content);
//        $this->assertEquals($channel->getLocation(), $content->channel);
//
//        // Две одинаковых картинки добавить нельзя
//        $this->expectException(\RuntimeException::class);
//        $this->client->getChannelImageResource()
//            ->addImageToChannel($image, $channel)
//            ->exec();
//    }
//
//    /**
//     * @depends testCreateChannel
//     * @param Channel $channel
//     */
//    public function testGetImagesByChannel(Channel $channel)
//    {
//        $content = $this->client->getChannelResource()
//            ->getImages($channel->getLocation())
//            ->exec()
//            ->getArrayResult();
//
//        $this->assertApiResponseCollection(ChannelImage::class, $content);
//    }
//
    /**
     * @depends testCreateCategory
     * @param Category $category
     */
    public function testDeleteCategory(Category $category)
    {
        /** @var Category $content */
        $content = $this->client->getCategoryResource()
            ->delete($category)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Category::class, $content);
    }

}
