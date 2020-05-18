<?php

namespace EpgClient\Tests\Integration;

use EpgClient\Context\Channel;
use EpgClient\Context\Provider;
use EpgClient\Tests\CustomApiTestCase;

/**
 * @package Tests\Integration
 * @group Integration
 */
class ProviderResourceTest extends CustomApiTestCase
{
    public function testGetProviders()
    {
        $content = $this->client->getProviderResource()
            ->get()
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(Provider::class, $content);

        return current($content);
    }

    /**
     * @depends testGetProviders
     * @param Provider $provider
     * @return Channel
     */
    public function testGetChannels(Provider $provider)
    {
        $content = $this->client->getProviderResource()
            ->getChannels($provider->getLocation())
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(Channel::class, $content);

        return reset($content);
    }

    /**
     * @depends      testGetChannels
     * @param Channel $channel
     */
    public function testGetChannelByExternalId(Channel $channel)
    {
        $content = $this->client->getProviderResource()
            ->getChannels($channel->provider)
            ->addFilter('externalId', $channel->externalId)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Channel::class, $content);
    }

}
