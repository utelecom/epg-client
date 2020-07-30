<?php

namespace EpgClient\Resource;

use EpgClient\Context\Channel;

class AccountProgramResource extends AbstractResource
{
    const FILTER_CHANNEL = 'channel';
    const FILTER_PERIOD = 'period';
    const PERIOD_NOW = 'now';

    protected static $baseLocation = '/api/account_programs';

    public function get($id = null)
    {
        if (!$id) {
            throw new \InvalidArgumentException("Use getByChannel() or getByChannelId() to retrieve collection");
        }

        parent::get();
        $id and $this->location .= '/' . $id;

        return $this;
    }

    public function getByChannel(Channel $channel)
    {
        $this->reset();
        $this->addFilter(self::FILTER_CHANNEL, $channel->getId());
        $this->method = 'GET';

        return $this;
    }

    public function getByChannelId($channelId)
    {
        $this->reset();
        $this->addFilter(self::FILTER_CHANNEL, $channelId);
        $this->method = 'GET';

        return $this;
    }

    /**
     * now        - Current program
     * latest     - Current program with the several next ones (limited by `itemsPerPage`)
     * today      - Today programs
     * timestamp  - Any timestamp in the middle of the day, based on which the start and end of the day will be calculated
     * YYYY-mm-dd - Date of the day
     *
     * @param string $period
     */
    public function setPeriod($period)
    {
        $this->addFilter(self::FILTER_PERIOD, $period);
    }
}
