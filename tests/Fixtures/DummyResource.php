<?php

namespace EpgClient\Tests\Fixtures;

use EpgClient\Resource\AbstractResource;

class DummyResource extends AbstractResource
{
    protected static $baseLocation = '/api/dummies';
}
