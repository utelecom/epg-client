<?php

namespace EpgClient;

interface ConfigInterface
{
    const API_URL = 'API_URL';
    const API_ADMIN = 'API_ADMIN';
    const API_PASSWORD = 'API_PASSWORD';
    const ACCOUNT_NAME = 'ACCOUNT_NAME';
    const ACCOUNT_LOCATION = 'ACCOUNT_LOCATION';
    const API_TOKEN = '_api_token';
    const API_TOKEN_REFRESH = '_api_token_refresh';

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);
}
