<?php

namespace EpgClient\Tests\Integration;

use EpgClient\Context\Genre;
use EpgClient\Tests\CustomApiTestCase;

/**
 * @package Tests\Integration
 * @group Integration
 */
class GenreResourceTest extends CustomApiTestCase
{
    public function testGetGenres()
    {
        $content = $this->client->getGenreResource()
            ->get()
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(Genre::class, $content);

        return current($content);
    }

    public function testCreateGenre()
    {
        $context = $this->client->contextFactory()->createGenre();
        $context->externalId = 'phpUnit Genre';
        $context->setTitle('phpUnit Genre', 'en');

        /** @var Genre $content */
        $content = $this->client->getGenreResource()
            ->post($context)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Genre::class, $content);
        $this->assertEquals('phpUnit Genre', $content->externalId);
        $this->assertEquals('phpUnit Genre', $content->__get('translations')['en']['title']);

        return $content;
    }

    /**
     * @depends testCreateGenre
     * @param Genre $genre
     */
    public function testGetChannel(Genre $genre)
    {
        /** @var Genre $content */
        $content = $this->client->getGenreResource()
            ->get($genre->getLocation())
            ->withGroup('translations')
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Genre::class, $content);
        $this->assertEquals('phpUnit Genre', $content->externalId);
        $this->assertEquals('phpUnit Genre', $content->__get('translations')['en']['title']);
    }

    /**
     * @depends testCreateGenre
     * @param Genre $genre
     */
    public function testEditGenre(Genre $genre)
    {
        $genre->setTitle('phpUnit Genre New', 'en');
        $genre->setTitle('phpUnit Genre New', 'uk');

        /** @var Genre $content */
        $content = $this->client->getGenreResource()
            ->put($genre)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Genre::class, $content);
        $this->assertEquals('phpUnit Genre', $content->externalId);
        $this->assertEquals('phpUnit Genre New', $content->__get('translations')['en']['title']);
        $this->assertEquals('phpUnit Genre New', $content->__get('translations')['uk']['title']);
    }

    /**
     * @depends testCreateGenre
     * @param Genre $Genre
     */
    public function testDeleteGenre(Genre $Genre)
    {
        /** @var Genre $content */
        $content = $this->client->getGenreResource()
            ->delete($Genre)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Genre::class, $content);
    }

}
