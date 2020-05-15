<?php

namespace EpgClient\Tests\Integration;

use EpgClient\ConfigInterface;
use EpgClient\Context\Account;
use EpgClient\Context\Channel;
use EpgClient\Tests\CustomApiTestCase;

/**
 * @package Tests\Integration
 * @group Integration
 */
class AccountResourceTest extends CustomApiTestCase
{
    public function testGetAccountByName()
    {
        $accountName = $this->config->get(ConfigInterface::ACCOUNT_NAME);
        $content = $this->client->getAccountResource()
            ->get()
            ->addFilter('name', $accountName)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Account::class, $content);
        return $content;
    }

    /**
     * @depends      testGetAccountByName
     * @param Account $account
     */
    public function testAddChannelToAccount(Account $account)
    {
        $channel = $this->client->contextFactory()->createChannel();
        $channel->externalId = 'phpUnit ' . time();
        $channel->account = $account;

        /** @var Channel $content */
        $content = $this->client->getChannelResource()
            ->addChannelToAccount($channel, $account)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Channel::class, $content);
        $this->assertEquals($account->getLocation(), $content->account);
        $this->assertEquals($channel->externalId, $content->externalId);
    }

    /**
     * @depends      testGetAccountByName
     * @param Account $account
     * @return Channel
     */
    public function testGetChannels(Account $account)
    {
        $content = $this->client->getAccountResource()
            ->getChannels($account->getLocation())
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
        $content = $this->client->getAccountResource()
            ->getChannels($channel->account)
            ->addFilter('externalId', $channel->externalId)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Channel::class, $content);
    }

    protected function setUp()
    {
        if (empty($_ENV['ACCOUNT_NAME'])) {
            $this->markTestSkipped('You must set $_ENV[\'ACCOUNT_NAME\'] first!');
        }
        parent::setUp();
        $this->config->set(ConfigInterface::ACCOUNT_NAME, $_ENV['ACCOUNT_NAME']);
    }
}
