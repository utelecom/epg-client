<?php

namespace EpgClient\Context;

/**
 * @package EpgClient\Context
 * @property string|null     $provider
 * @property string|null     $account
 * @property string          $externalId
 * @property string|null     $poster
 * @property-read string     $title
 * @property-read null|array $translations       look at CategoryResource::GROUP_TRANSLATIONS
 * @property null|Category[] $providerCategories look at CategoryResource::GROUP_PARENT
 */
class Category extends AbstractContext
{
    public function setTitle($title, $locale)
    {
        $translations = $this->translations;
        $translations[$locale]['locale'] = $locale;
        $translations[$locale]['title'] = $title;

        $this->translations = $translations;
    }
}
