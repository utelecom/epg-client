<?php

namespace EpgClient\Resource;

use EpgClient\Context\Account;
use EpgClient\Context\Category;

class CategoryResource extends AbstractResource
{
    protected static $baseLocation = '/api/categories';

//    public function getImages($channelLocation)
//    {
//        $this->reset();
//        $this->location = $channelLocation . '/images';
//        $this->method = 'GET';
//
//        return $this;
//    }
//
    public function addCategoryToAccount(Category $context, Account $account)
    {
        $context->account = $account->getLocation();

        return $this->post($context);
    }
}
