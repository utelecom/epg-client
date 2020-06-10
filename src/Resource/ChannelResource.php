<?php

namespace EpgClient\Resource;

use EpgClient\Context\Account;
use EpgClient\Context\Channel;

class ChannelResource extends AbstractResource
{
    const GROUP_TRANSLATIONS = 'translations';
    const GROUP_IMAGES = 'images';
    const GROUP_PARENT = 'parent';

    protected static $baseLocation = '/api/channels';

    public function getImages($channelLocation)
    {
        $this->reset();
        $this->location = $channelLocation . '/images';
        $this->method = 'GET';

        return $this;
    }

    public function addChannelToAccount(Channel $channel, Account $account)
    {
        $channel->account = $account->getLocation();

        return $this->post($channel);
    }
}
