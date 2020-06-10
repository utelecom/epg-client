<?php

namespace EpgClient\Tests\Integration;

use EpgClient\ConfigInterface;
use EpgClient\Context\Account;
use EpgClient\Context\Category;
use EpgClient\Context\Channel;
use EpgClient\Context\Genre;
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

    /**
     * @depends      testGetAccountByName
     * @param Account $account
     */
    public function testAddCategoryToAccount(Account $account)
    {
        $context = $this->client->contextFactory()->createCategory();
        $context->externalId = 'phpUnit Category' . time();
        $context->account = $account;

        /** @var Channel $content */
        $content = $this->client->getCategoryResource()
            ->addCategoryToAccount($context, $account)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Category::class, $content);
        $this->assertEquals($account->getLocation(), $content->account);
        $this->assertEquals($context->externalId, $content->externalId);
    }

    /**
     * @depends      testGetAccountByName
     * @param Account $account
     * @return Channel
     */
    public function testGetCategories(Account $account)
    {
        $content = $this->client->getAccountResource()
            ->getCategories($account->getLocation())
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(Category::class, $content);

        return reset($content);
    }

    /**
     * @depends      testGetCategories
     * @param Category $category
     */
    public function testGetCategoryByExternalId(Category $category)
    {
        $content = $this->client->getAccountResource()
            ->getCategories($category->account)
            ->addFilter('externalId', $category->externalId)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Category::class, $content);
    }

    /**
     * @depends      testGetAccountByName
     * @param Account $account
     * @return Channel
     */
    public function testGetGenres(Account $account)
    {
        $content = $this->client->getAccountResource()
            ->getGenres($account->getLocation())
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(Genre::class, $content);

        return reset($content);
    }

    /**
     * @depends      testGetGenres
     * @param Genre $genre
     */
    public function testGetGenreByExternalId(Genre $genre)
    {
        $content = $this->client->getAccountResource()
            ->getGenres($genre->account)
            ->addFilter('externalId', $genre->externalId)
            ->exec()
            ->getSingleResult();

        $this->assertApiResponseSingleResult(Genre::class, $content);
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
