<?php

namespace EpgClient\Tests\Integration;

use EpgClient\Context\Category;
use EpgClient\Context\CategoryImage;
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

    /**
     * @depends testCreateCategory
     * @param Category $category
     */
    public function testAddImageToCategory(Category $category)
    {
        $image = $this->client->contextFactory()->createCategoryImage();
        $image->uri = 'http://localhost/phpunit.png';

        /** @var CategoryImage $content */
        $content = $this->client->getCategoryImageResource()
            ->addImageToCategory($image, $category)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(CategoryImage::class, $content);
        $this->assertEquals($category->getLocation(), $content->category);

        // Две одинаковых картинки добавить нельзя
        $this->expectException(\RuntimeException::class);
        $this->client->getCategoryImageResource()
            ->addImageToCategory($image, $category)
            ->exec();
    }

    /**
     * @depends testCreateCategory
     * @param Category $category
     */
    public function testGetImagesByCategory(Category $category)
    {
        $content = $this->client->getCategoryResource()
            ->getImages($category->getLocation())
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(CategoryImage::class, $content);
    }

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
