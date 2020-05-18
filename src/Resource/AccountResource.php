<?php

namespace EpgClient\Resource;

class AccountResource extends AbstractResource
{
    protected static $baseLocation = '/api/accounts';

    public function getChannels($accountLocation)
    {
        $this->reset();
        $this->location = $accountLocation . '/channels';
        $this->method = 'GET';

        return $this;
    }

    public function getCategories($accountLocation)
    {
        $this->reset();
        $this->location = $accountLocation . '/categories';
        $this->method = 'GET';

        return $this;
    }
}
