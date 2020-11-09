<?php

namespace EpgClient\Resource;

use EpgClient\Context\Channel;

class AccountProgramResource extends AbstractResource
{
    const FILTER_START = 'start';
    const FILTER_STOP = 'stop';
    const FILTER_PERIOD = 'period';
    const FILTER_CHANNEL = 'channel';
    const FILTER_CHANNELS = 'channels';
    const FILTER_TITLE = 'title';
    const FILTER_CATEGORY_NAME = 'categoryName';
    const FILTER_GENRE_NAME = 'genreName';

    const PERIOD_NOW = 'now';
    const PERIOD_LATEST = 'latest';
    const PERIOD_TODAY = 'today';
    const PERIOD_WEEK = 'week';
    const PERIOD_MONTH = 'month';

    const ORDER_BY_START = 'start';

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
        parent::get();

        return $this->addFilter(self::FILTER_CHANNEL, $channel->getId());
    }

    public function getByChannelId($channelId)
    {
        parent::get();

        return $this->addFilter(self::FILTER_CHANNEL, $channelId);
    }

    public function getByChannelsId(array $channelsId)
    {
        parent::get();

        return $this->addFilter(self::FILTER_CHANNELS, implode(',', $channelsId));
    }

    /**
     * @param string $title
     *
     * @return $this
     * @deprecated please use addFilter($resource::FILTER_TITLE, 'Some Title')
     */
    public function getByTitle($title)
    {
        parent::get();

        return $this->addFilter(self::FILTER_TITLE, $title);
    }

    /**
     * now        - Current program
     * latest     - Current program with the several next ones (limited by `itemsPerPage`)
     * today      - Today programs
     * timestamp  - Any timestamp in the middle of the day, based on which the start and end of the day will be
     * calculated YYYY-mm-dd - Date of the day
     *
     * @param string $period
     *
     * @return AccountProgramResource
     */
    public function setPeriod($period)
    {
        return $this->addFilter(self::FILTER_PERIOD, $period);
    }

    /**
     * @param int $timestamp
     *
     * @return AccountProgramResource
     */
    public function setStart($timestamp)
    {
        return $this->addFilter(self::FILTER_START, $timestamp);
    }

    /**
     * @param int $timestamp
     *
     * @return AccountProgramResource
     */
    public function setStop($timestamp)
    {
        return $this->addFilter(self::FILTER_STOP, $timestamp);
    }
}
