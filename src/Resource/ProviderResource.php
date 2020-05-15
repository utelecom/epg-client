<?php

namespace EpgClient\Resource;

class ProviderResource extends AbstractResource
{
    protected static $baseLocation = '/api/providers';

    public function getChannels($providerLocation)
    {
        $this->reset();
        $this->location = $providerLocation . '/channels';
        $this->method = 'GET';

        return $this;
    }
}
