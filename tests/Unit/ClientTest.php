<?php

namespace EpgClient\Tests\Unit;

use EpgClient\Client;
use EpgClient\Resource\AbstractResource;
use EpgClient\Tests\CustomTestCase;
use EpgClient\Tests\Fixtures\DummyConfig;

/**
 * Class ClientTest
 * @package Tests\Unit
 * @group Unit
 */
class ClientTest extends CustomTestCase
{
    public function testClassExist()
    {
        $this->assertTrue(class_exists(Client::class));
    }

    public function testInstantiate()
    {
        $config = new DummyConfig();
        $client = new Client($config);
        $this->assertInstanceOf(Client::class, $client);
        return $client;
    }

    /**
     * @depends      testInstantiate
     * @dataProvider getResourceProvider
     * @param string $resourceName
     * @param Client $client
     */
    public function testGetResource($resourceName, $client)
    {
        $method = "get{$resourceName}Resource";
        $this->assertTrue(is_callable([$client, $method]));

        $this->assertInstanceOf(AbstractResource::class, call_user_func([$client, $method], $resourceName));
    }

    public function getResourceProvider()
    {
        return [
            ['Account'],
            ['Channel'],
            ['Provider'],
        ];
    }
}
