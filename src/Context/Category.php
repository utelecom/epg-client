<?php

namespace EpgClient\Context;

/**
 * @package EpgClient\Context
 * @property string|null    $provider
 * @property string|null    $account
 * @property string         $externalId
 * @property-read string    $title
 * @property-read array     $translations
 * @property Category[]     $providerCategories
// * @property ChannelImage[] $images
// * @property string         $logo
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
