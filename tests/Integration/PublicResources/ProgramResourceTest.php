<?php

namespace EpgClient\Tests\Integration\PublicResources;

use EpgClient\Resource\AccountProgramResource;
use EpgClient\Tests\CustomAccountApiTestCase;

/**
 * @package Tests\Integration
 * @group Integration
 */
class ProgramResourceTest extends CustomAccountApiTestCase
{
    public function testGetAccountProgramResource()
    {
        $content = $this->client->getAccountProgramResource()
            ->getByChannelId(1)
            ->setPeriod(AccountProgramResource::PERIOD_NOW)
            ->exec()
            ->getArrayResult();

        $this->assertApiResponseCollection(AccountProgramResource::class, $content);

        return $content;
    }
}
