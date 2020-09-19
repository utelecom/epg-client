<?php

namespace EpgClient\Context;

/**
 * Class AccountGenre - Account related genre
 *
 * @package EpgClient\AccountContext
 *
 * @property-read string $id
 * @property-read string $channelId
 * @property-read string $categoryId
 * @property-read string $categoryTitle
 * @property-read array  $genresId
 * @property-read array  $genresTitle
 * @property-read string $start
 * @property-read int    $startUtc
 * @property-read string $stop
 * @property-read int    $stopUtc
 * @property-read int    $duration
 * @property-read string $title
 * @property-read string $announce
 * @property-read string $description
 * @property-read string $rating
 * @property-read string $year
 * @property-read string $season
 * @property-read string $episode
 * @property-read array  $countries
 * @property-read array  $productions
 * @property-read array  $directors
 * @property-read array  $actors
 * @property-read array  $composers
 * @property-read array  $producers
 * @property-read string $posterPortrait
 * @property-read string $catchupUrl
 * @property-read string $censored
 */
class AccountProgram extends AbstractContext
{

}
