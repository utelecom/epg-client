# HTTP Client for EPG service

## Installation

```
$ composer require utg/epg-client ^1.0
```

Then you need to implement `\EpgClient\ConfigInterface` on you project. 
It would be persistent storage for client config variables.

## Get account specific entities

Client initialization by read-only api key:

```
/** @var \EpgClient\ConfigInterface $config */
$config = new YourConfig();
$config->set($config::API_URL, 'https://<api_url>');
$config->set($config::API_KEY, '<your_api_key>');

$client = new EpgClient\Client($config);
$client->setAuthType(EpgClient\Client::AUTH_TYPE_API_KEY);
$client->setAcceptLanguage(EpgClient\Client::LANG_UK); #optional
```

Channels:

```
/** @var EpgClient\Context\AccountChannel[] $channels */
$channels = $client->getAccountChannelResource()
    ->get()
    ->exec()
    ->getArrayResult();

/** @var EpgClient\Context\AccountChannel $channel */
$channel = $client->getAccountChannelResource()
    ->get($channelId)
    ->exec()
    ->getSingleResult();
```

Categories:

```
/** @var EpgClient\Context\AccountCategory[] $categories */
$categories = $client->getAccountCategoryResource()
    ->get()
    ->exec()
    ->getArrayResult();

/** @var EpgClient\Context\AccountCategory $categorie */
$category = $client->getAccountCategoryResource()
    ->get($categoryId)
    ->exec()
    ->getSingleResult();
```

Genres:

```
/** @var EpgClient\Context\AccountGenre[] $genres */
$genres = $client->getAccountGenreResource()
    ->get()
    ->exec()
    ->getArrayResult();

/** @var EpgClient\Context\AccountGenre $genre */
$genre = $client->getAccountGenreResource()
    ->get($genreId)
    ->exec()
    ->getSingleResult();
```

Programs:

```
/**
 * You can get program collection by instance of Channel
 *
 * @var EpgClient\Context\Channel $channel 
 * @var EpgClient\Context\AccountProgram[] $programs 
 */
$programs = $client->getAccountProgramResource()
    ->getByChannel($channel)
    ->setPeriod(AccountProgramResource::PERIOD_NOW)
    ->exec()
    ->getArrayResult();

/**
 * Or you cat get program collection by channel id
 *
 * @var string $channelId 
 * @var EpgClient\Context\AccountProgram[] $programs 
 */
$programs = $client->getAccountProgramResource()
    ->getByChannelId($channelId)
    ->setPeriod(AccountProgramResource::PERIOD_TODAY)
    ->exec()
    ->getArrayResult();

/**
 * @var string $programId 
 * @var EpgClient\Context\AccountProgram $program 
 */
$program = $client->getAccountProgramResource()
   ->get($programId)
   ->exec()
   ->getSingleResult();
```

## Manage entities 

Client initialization by login/password

```
/** @var \EpgClient\ConfigInterface $config */
$config = new YourConfig();

$config->set($config::API_URL, 'https://<api_url>');
$config->set($config::API_ADMIN, '<your_admin_user>');
$config->set($config::API_PASSWORD, '<your_admin_password>');

$client = new EpgClient\Client($config);

# Optional client settings
$client->setAcceptLanguage(EpgClient\Client::LANG_UK);
```

Channels (full example):

```
/** @var EpgClient\Context\AccountChannel[] $channels */
$channels = $client->getAccountResource()
    ->getChannels()
    ->disablePagination()                                               # optional: pagination disable
    ->withGroup(EpgClient\Resource\ChannelResource::GROUP_TRANSLATIONS) # optional: translations, images, ...
    ->setLanguage(EpgClient\Client::LANG_UK)                            # optional: set language only for one request
    ->exec()
    ->getArrayResult();
```

Categories (short example):

```
/** @var EpgClient\Context\AccountCategory[] $categories */
$categories = $client->getAccountResource()
    ->getCategories()
    ->exec()
    ->getArrayResult();
```

Genres:

```
/** @var EpgClient\Context\AccountGenre[] $genres */
$genres = $client->getAccountResource()
    ->getGenres()
    ->exec()
    ->getArrayResult();
```

### Coming soon later, for another example please look at tests/Integration
