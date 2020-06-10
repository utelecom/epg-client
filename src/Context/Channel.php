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
 * @property string              $logo
 * @property integer             $position
 * @property bool                $adult
 * @property-read null|array     $translations    look at ChannelResource::GROUP_TRANSLATIONS
 * @property null|Channel        $providerChannel look at ChannelResource::GROUP_PARENT
 * @property null|ChannelImage[] $images          look at ChannelResource::GROUP_IMAGES
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
