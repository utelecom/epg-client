<?php

namespace EpgClient\Resource;

class AccountGenreResource extends AbstractResource
{
    const FILTER_ID = 'id';

    protected static $baseLocation = '/api/account_genres';

    public function get($id = null)
    {
        parent::get();
        $id and $this->location .= '/' . $id;

        return $this;
    }
}
