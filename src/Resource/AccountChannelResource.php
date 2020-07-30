<?php

namespace EpgClient\Resource;

class AccountChannelResource extends AbstractResource
{
    const FILTER_ID = 'id';

    protected static $baseLocation = '/api/account_channels';

    public function get($id = null)
    {
        parent::get();
        $id and $this->location .= '/' . $id;

        return $this;
    }
}
