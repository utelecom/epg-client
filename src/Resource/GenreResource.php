<?php

namespace EpgClient\Resource;

class GenreResource extends AbstractResource
{
    const GROUP_TRANSLATIONS = 'translations';
    const GROUP_PARENT = 'parent';

    protected static $baseLocation = '/api/genres';

}
