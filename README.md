# HTTP Client for EPG sevice

## Installation

```
$ composer require utelecom/epg-client
```

Than you need to implement `\EpgClient\ConfigInterface` on you project. 
It's would be a persistent storage for client config variables.

## Initialization

```php
<?php

/** @var \EpgClient\ConfigInterface $config */
$config = new YourConfig();

$config->set($config::API_URL, 'https://<api_url>');
$config->set($config::API_ADMIN, '<your_admin_user>');
$config->set($config::API_PASSWORD, '<your_admin_password>');
$config->set($config::ACCOUNT_LOCATION, '/api/account/<your_account_id>'); // TODO will deprecate in future releases
```

## Usage

```php
<?php

/** @var \EpgClient\ConfigInterface $config */
$config = new YourConfig();
$client = new EpgClient\Client($config);

# Coming soon later, please look at tests/Integration 
```

