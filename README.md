# HTTP Client for EPG service

## Installation

```
$ composer require utg/epg-client ^0.1
```

Then you need to implement `\EpgClient\ConfigInterface` on you project. 
It would be persistent storage for client config variables.

## Initialization

```php
<?php

/** @var \EpgClient\ConfigInterface $config */
$config = new YourConfig();

$config->set($config::API_URL, 'https://<api_url>');
$config->set($config::API_ADMIN, '<your_admin_user>');
$config->set($config::API_PASSWORD, '<your_admin_password>');
```

## Usage

```php
<?php

/** @var \EpgClient\ConfigInterface $config */
$config = new YourConfig();
$client = new EpgClient\Client($config);

# Optional client settings
$client->setAcceptLanguage(EpgClient\Client::LANG_UK);

##
## Everyday activity
##

### Get channels
$client->getAccountResource()->getChannels()                            # Full example:
    ->disablePagination()                                               # optional: pagination disable
    ->withGroup(EpgClient\Resource\ChannelResource::GROUP_TRANSLATIONS) # optional: translations, images, ...
    ->setLanguage(EpgClient\Client::LANG_UK)                            # optional: set language only for one request
    ->exec()
    ->getArrayResult();

### Get categories
$client->getAccountResource()->getCategories()->exec()->getArrayResult();

### Get genres
$client->getAccountResource()->getGenres()->exec()->getArrayResult();

```

## Coming soon later, for another example please look at tests/Integration
