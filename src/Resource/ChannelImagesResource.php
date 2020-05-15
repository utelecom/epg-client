<?php

namespace EpgClient\Resource;

use EpgClient\Context\Channel;
use EpgClient\Context\ChannelImage;

class ChannelImagesResource extends AbstractResource
{
    protected static $baseLocation = '/api/channel_images';

    /**
     * @param ChannelImage $channelImage
     * @param Channel $channel
     * @return $this
     */
    public function addImageToChannel(ChannelImage $channelImage, Channel $channel)
    {
        $channelImage->channel = $channel->getLocation();

        return $this->post($channelImage);
    }
}
