<?php

namespace EpgClient\Tests;

use EpgClient\Client;
use EpgClient\ConfigInterface;
use EpgClient\Tests\Fixtures\DummyConfig;

class CustomApiTestCase extends CustomTestCase
{
    /** @var Client */
    protected $client;
    /** @var ConfigInterface */
    protected $config;

    protected function setUp()
    {
        if (!isset($_ENV['API_URL'], $_ENV['API_ADMIN'], $_ENV['API_PASSWORD'])) {
            $this->markTestSkipped("Missed required ENV variables!");
        }
        $this->config = new DummyConfig([
            DummyConfig::API_URL      => $_ENV['API_URL'],
            DummyConfig::API_ADMIN    => $_ENV['API_ADMIN'],
            DummyConfig::API_PASSWORD => $_ENV['API_PASSWORD'],
        ]);
        $this->client = new Client($this->config);
    }
}
