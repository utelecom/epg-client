<?php

namespace EpgClient\Resource;

class AccountCategoryResource extends AbstractResource
{
    const FILTER_ID = 'id';

    protected static $baseLocation = '/api/account_categories';

    public function get($id = null)
    {
        parent::get();
        $id and $this->location .= '/' . $id;

        return $this;
    }
}
