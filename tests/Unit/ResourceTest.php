<?php

namespace EpgClient\Tests\Unit;

use EpgClient\Client;
use EpgClient\ContextFactory;
use EpgClient\Resource\AbstractResource;
use EpgClient\Tests\CustomTestCase;
use EpgClient\Tests\Fixtures\DummyConfig;
use EpgClient\Tests\Fixtures\DummyContext;
use EpgClient\Tests\Fixtures\DummyResource;
use EpgClient\Tests\Fixtures\DummyResponse;

/**
 * Class ResourceTest
 * @package Tests\Unit
 * @group Unit
 */
class ResourceTest extends CustomTestCase
{
    /** @var AbstractResource|\PHPUnit_Framework_MockObject_MockObject */
    private $resource;
    /** @var Client|\PHPUnit_Framework_MockObject_MockBuilder */
    private $clientMock;
    /** @var DummyResponse|\PHPUnit_Framework_MockObject_MockObject */
    private $response;

    /**
     * @dataProvider getCollectionProvider
     * @param array $responseData
     */
    public function testGetCollection($responseData)
    {
        $this->response->method('getStatusCode')->willReturn(200);
        $this->clientMock->method('responseToArray')->willReturn($responseData);
        $this->clientMock->expects($this->once())->method('request')->with('GET', '/api/dummies', null);

        /** @var DummyContext[] $content */
        $content = $this->resource
            ->get()
            ->exec()
            ->getArrayResult();
        $this->assertApiResponseCollection(DummyContext::class, $content);

        /** @var DummyContext $item */
        $item = reset($content);
        $this->assertEquals('/api/dummies/1', $item->getLocation());
        $this->assertEquals('Dummy', $item->getType());
    }

    public function getCollectionProvider()
    {
        return [
            [
                [
                    '@context'         => '/api/contexts/dummy',
                    '@id'              => '/api/dummies',
                    '@type'            => 'hydra:Collection',
                    'hydra:member'     =>
                        [
                            [
                                '@id'           => '/api/dummies/1',
                                '@type'         => 'Dummy',
                                'nestedContext' => [
                                    '@id'      => '/api/dummies/2',
                                    '@type'    => 'Dummy',
                                ],
                            ],
                        ],
                    'hydra:totalItems' => 1,
                ],
            ]
        ];
    }

    /**
     * @dataProvider getOneProvider
     * @param array $responseData
     */
    public function testGetOneByLocation($responseData)
    {
        $this->response->method('getStatusCode')->willReturn(200);
        $this->clientMock->method('responseToArray')->willReturn($responseData);
        $this->clientMock->expects($this->once())->method('request')->with('GET', '/api/dummies/1', null);

        /** @var DummyContext $content */
        $content = $this->resource
            ->get('/api/dummies/1')
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(DummyContext::class, $content);
    }

    public function getOneProvider()
    {
        return [
            [
                [
                    '@context'         => '/api/contexts/Dummy',
                    '@id'              => '/api/dummies/1',
                    '@type'            => 'Dummy',
                ],
            ]
        ];
    }

    protected function setUp()
    {
        $config = new DummyConfig();
        $this->response = $this->createMock(DummyResponse::class);
        $this->clientMock = $this->getMockBuilder(Client::class)->setConstructorArgs([$config])->getMock();
        $this->clientMock->method('request')->willReturn($this->response);
        $this->clientMock->method('contextFactory')->willReturn(new ContextFactory([
            'dummy' => DummyContext::class,
        ]));
        $this->resource = new DummyResource($this->clientMock);
    }
}
