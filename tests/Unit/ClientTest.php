<?php

namespace EpgClient\Tests\Unit;

use EpgClient\Client;
use EpgClient\JWTPayload;
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
     * @dataProvider getResourceProvider
     * @param string $resourceName
     */
    public function testGetResource($resourceName)
    {
        $config = new DummyConfig();
        $payload = new JWTPayload('');
        $clientMock = $this->getMockBuilder(Client::class)
            ->setMethods(['getTokenPayload'])
            ->setConstructorArgs([$config])
            ->getMock();
        $clientMock->method('getTokenPayload')->willReturn($payload);

        $method = "get{$resourceName}Resource";
        $this->assertTrue(is_callable([$clientMock, $method]));

        $this->assertInstanceOf(AbstractResource::class, call_user_func([$clientMock, $method], $resourceName));
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
