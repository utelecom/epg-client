<?php

namespace EpgClient\Context;

/**
 * @package EpgClient\Context
 * @property string|null     $provider
 * @property string|null     $account
 * @property string          $externalId
 * @property-read string     $title
 * @property-read null|array $translations   look at GenreResource::GROUP_TRANSLATIONS
 * @property null|Genre[]    $providerGenres look at GenreResource::GROUP_PARENT
 */
class Genre extends AbstractContext
{
    public function setTitle($title, $locale)
    {
        $translations = $this->translations;
        $translations[$locale]['locale'] = $locale;
        $translations[$locale]['title'] = $title;

        $this->translations = $translations;
    }
}
