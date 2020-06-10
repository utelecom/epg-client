<?php

namespace EpgClient\Tests\Integration;

use EpgClient\Client;
use EpgClient\Context\Account;
use EpgClient\Context\Category;
use EpgClient\Context\Channel;
use EpgClient\Context\Genre;
use EpgClient\Context\Provider;
use EpgClient\Tests\CustomApiTestCase;

/**
 * Class ResourceTest
 * @package Tests\Integration
 * @group Integration
 */
class ResourceTest extends CustomApiTestCase
{
    /**
     * @dataProvider getCollectionProvider
     * @param string $expectedClass
     * @param string $resourceName
     * @return Provider[]
     */
    public function testGetCollection($expectedClass, $resourceName)
    {
        /** @var Provider[] $content */
        $content = $this->client->getResource($resourceName)
            ->get()
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection($expectedClass, $content);
        return $content;
    }

    public function getCollectionProvider()
    {
        return [
            [Provider::class, Client::PROVIDER],
            [Account::class, Client::ACCOUNT],
            [Channel::class, Client::CHANNEL],
            [Category::class, Client::CATEGORY],
            [Genre::class, Client::GENRE],
        ];
    }
}
