<?php

namespace EpgClient\Context;

/**
 * Class Channel
 *
 * @package EpgClient\Context
 * @property string|null         $provider
 * @property string|null         $account
 * @property string              $externalId
 * @property-read string         $title
 * @property-read string         $genreTitle      Channel genre name
 * @property string              $logo
 * @property integer             $position
 * @property bool                $adult
 * @property bool                $enabled
 * @property bool                $visible
 * @property int                 $catchupDays
 * @property string              $catchupTemplate Ex: https://host/id/video-{utc}-{duration}.m3u8?secret={secret}
 * @property-read null|array     $translations    look at ChannelResource::GROUP_TRANSLATIONS
 * @property null|Channel        $providerChannel look at ChannelResource::GROUP_PARENT
 * @property null|ChannelImage[] $images          look at ChannelResource::GROUP_IMAGES
 */
class Channel extends AbstractContext
{
    /**
     * @param string $title
     * @param string $locale
     */
    public function setTitle($title, $locale)
    {
        $translations = $this->translations;
        $translations[$locale]['locale'] = $locale;
        $translations[$locale]['title'] = $title;

        $this->translations = $translations;
    }

    /**
     * @param string $title
     * @param string $locale
     */
    public function setGenreTitle($title, $locale)
    {
        $translations = $this->translations;
        $translations[$locale]['locale'] = $locale;
        $translations[$locale]['genre'] = $title;

        $this->translations = $translations;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $parts = array_reverse(explode('/', $this->getLocation()));
        return reset($parts);
    }
}
