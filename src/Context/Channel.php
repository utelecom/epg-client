<?php

namespace EpgClient\Context;

/**
 * Class Channel
 *
 * @package EpgClient\Context
 * @property string|null    $provider
 * @property string|null    $account
 * @property string         $externalId
 * @property-read string    $title
 * @property-read array     $translations
 * @property Channel|null   $providerChannel
 * @property ChannelImage[] $images
 * @property string         $logo
 * @property integer        $position
 * @property bool           $adult
 */
class Channel extends AbstractContext
{
    public function setTitle($title, $locale)
    {
        $translations = $this->translations;
        $translations[$locale]['locale'] = $locale;
        $translations[$locale]['title'] = $title;

        $this->translations = $translations;
    }
}
