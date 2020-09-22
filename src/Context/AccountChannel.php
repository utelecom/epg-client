<?php

namespace EpgClient\Context;

/**
 * Class AccountChannel - Account related channel
 *
 * @package EpgClient\AccountContext
 *
 * @property-read string $id
 * @property-read int    $position
 * @property-read bool   $adult
 * @property-read string $logo
 * @property-read string $title
 * @property-read string $genre
 * @property-read string $genrePoster
 * @property-read string $siteUrl
 * @property-read bool   $hasEpg
 * @property-read bool   $catchupAvailable
 * @property-read int    $catchupFromUtc
 */
class AccountChannel extends AbstractContext
{

}
