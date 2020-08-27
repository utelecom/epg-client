<?php

namespace EpgClient\Tests;

use EpgClient\Client;
use EpgClient\ConfigInterface;
use EpgClient\Tests\Fixtures\DummyConfig;

class CustomAccountApiTestCase extends CustomTestCase
{
    /** @var Client */
    protected $client;
    /** @var ConfigInterface */
    protected $config;

    protected function setUp()
    {
        if (!isset($_ENV['API_URL'], $_ENV['API_KEY'])) {
            $this->markTestSkipped("Missed required ENV variables!");
        }
        $this->config = new DummyConfig([
            DummyConfig::API_URL      => $_ENV['API_URL'],
            DummyConfig::API_KEY    => $_ENV['API_KEY'],
        ]);
        $this->client = new Client($this->config);
        $this->client->setAuthType(Client::AUTH_TYPE_API_KEY);
    }
}
