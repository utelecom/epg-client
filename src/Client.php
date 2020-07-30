<?php

namespace EpgClient;

use EpgClient\Resource\AbstractResource;
use EpgClient\Token\InvalidJWTPayload;
use EpgClient\Token\JWTPayload;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 *
 * @package EpgClient
 *
 * @method \EpgClient\Resource\AccountChannelResource getAccountChannelResource()
 * @method \EpgClient\Resource\AccountCategoryResource getAccountCategoryResource()
 * @method \EpgClient\Resource\AccountGenreResource getAccountGenreResource()
 * @method \EpgClient\Resource\AccountProgramResource getAccountProgramResource()
 * @method \EpgClient\Resource\AccountResource getAccountResource()
 * @method \EpgClient\Resource\ChannelResource getChannelResource()
 * @method \EpgClient\Resource\ChannelImagesResource getChannelImageResource()
 * @method \EpgClient\Resource\ProviderResource getProviderResource()
 * @method \EpgClient\Resource\CategoryResource getCategoryResource()
 * @method \EpgClient\Resource\CategoryImagesResource getCategoryImageResource()
 * @method \EpgClient\Resource\GenreResource getGenreResource()
 */
class Client
{
    const AUTH_TYPE_JWT = 'jwt';
    const AUTH_TYPE_API_KEY = 'api_key';

    const LANG_UK = 'uk';
    const LANG_RU = 'ru';

    const ACCOUNT_CHANNEL = 'accountchannel';
    const ACCOUNT_CATEGORY = 'accountcategory';
    const ACCOUNT_GENRE = 'accountgenre';
    const ACCOUNT_PROGRAM = 'accountprogram';
    const ACCOUNT = 'account';
    const PROVIDER = 'provider';
    const CHANNEL = 'channel';
    const CHANNEL_IMAGE = 'channelimage';
    const CATEGORY = 'category';
    const CATEGORY_IMAGE = 'categoryimage';
    const GENRE = 'genre';

    protected static $apiResources = [
        self::ACCOUNT_CHANNEL  => \EpgClient\Resource\AccountChannelResource::class,
        self::ACCOUNT_CATEGORY => \EpgClient\Resource\AccountCategoryResource::class,
        self::ACCOUNT_GENRE    => \EpgClient\Resource\AccountGenreResource::class,
        self::ACCOUNT_PROGRAM  => \EpgClient\Resource\AccountProgramResource::class,
        self::ACCOUNT          => \EpgClient\Resource\AccountResource::class,
        self::PROVIDER         => \EpgClient\Resource\ProviderResource::class,
        self::CHANNEL          => \EpgClient\Resource\ChannelResource::class,
        self::CHANNEL_IMAGE    => \EpgClient\Resource\ChannelImagesResource::class,
        self::CATEGORY         => \EpgClient\Resource\CategoryResource::class,
        self::CATEGORY_IMAGE   => \EpgClient\Resource\CategoryImagesResource::class,
        self::GENRE            => \EpgClient\Resource\GenreResource::class,
    ];

    protected static $apiContexts = [
        self::ACCOUNT_CHANNEL  => \EpgClient\Context\AccountChannel::class,
        self::ACCOUNT_CATEGORY => \EpgClient\Context\AccountCategory::class,
        self::ACCOUNT_GENRE    => \EpgClient\Context\AccountGenre::class,
        self::ACCOUNT_PROGRAM  => \EpgClient\Context\AccountProgram::class,
        self::ACCOUNT          => \EpgClient\Context\Account::class,
        self::PROVIDER         => \EpgClient\Context\Provider::class,
        self::CHANNEL          => \EpgClient\Context\Channel::class,
        self::CHANNEL_IMAGE    => \EpgClient\Context\ChannelImage::class,
        self::CATEGORY         => \EpgClient\Context\Category::class,
        self::CATEGORY_IMAGE   => \EpgClient\Context\CategoryImage::class,
        self::GENRE            => \EpgClient\Context\Genre::class,
    ];

    /** @var ConfigInterface */
    private $config;
    /** @var ContextFactory */
    private $contextFactory;
    /** @var ResourceFactory */
    private $resourceFactory;
    /** @var \GuzzleHttp\Client */
    private $httpClient;
    /** @var string */
    private $acceptLanguage;
    /** @var string */
    private $authType = self::AUTH_TYPE_JWT;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function contextFactory()
    {
        if (!$this->contextFactory) {
            $this->contextFactory = new ContextFactory(self::$apiContexts);
        }

        return $this->contextFactory;
    }

    public function __call($name, $arguments)
    {
        if (preg_match('#^get(.+?)Resource$#', $name, $matches)) {
            return $this->resourceFactory()->build($matches[1]);
        }

        throw new \BadMethodCallException;
    }

    private function resourceFactory()
    {
        if (!$this->resourceFactory) {
            $payload = $this->getTokenPayload();
            $this->resourceFactory = new ResourceFactory(static::$apiResources, $this, $payload);
        }

        return $this->resourceFactory;
    }

    /**
     * @return JWTPayload
     * @throws InvalidJWTPayload
     */
    protected function getTokenPayload()
    {
        try {
            $jwt = $this->getToken();
            return new JWTPayload($jwt);
        } catch (InvalidJWTPayload $e) {
            $jwt = $this->refreshToken();
            return new JWTPayload($jwt);
        }
    }

    /**
     * @return string
     */
    private function getToken()
    {
        if ($this->config->get(ConfigInterface::API_TOKEN)) {
            return $this->config->get(ConfigInterface::API_TOKEN);
        }

        return $this->createToken();
    }

    /**
     * @return string
     */
    private function createToken()
    {
        $response = $this->getClient()->post('token/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json'    => [
                'login'    => $this->config->get(ConfigInterface::API_ADMIN),
                'password' => $this->config->get(ConfigInterface::API_PASSWORD),
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Can't get token");
        }

        $data = $this->responseToArray($response);
        $this->config->set(ConfigInterface::API_TOKEN, $data['token']);
        $this->config->set(ConfigInterface::API_TOKEN_REFRESH, $data['refresh_token']);

        return $this->config->get(ConfigInterface::API_TOKEN);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = new \GuzzleHttp\Client([
                'base_uri'    => $this->config->get(ConfigInterface::API_URL),
                'http_errors' => false,
                'cookies'     => true,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function responseToArray(ResponseInterface $response)
    {
        $response->getBody()->rewind();
        return json_decode($response->getBody()->getContents(), true);
    }

    private function refreshToken()
    {
        if (!$this->config->get(ConfigInterface::API_TOKEN_REFRESH)) {
            return $this->createToken();
        }

        $response = $this->getClient()->post('token/refresh', [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                'refresh_token' => $this->config->get(ConfigInterface::API_TOKEN_REFRESH),
            ])
        ]);

        if ($response->getStatusCode() !== 200) {
            return $this->createToken();
        }

        $data = $this->responseToArray($response);
        $this->config->set(ConfigInterface::API_TOKEN, $data['token']);
        $this->config->set(ConfigInterface::API_TOKEN_REFRESH, $data['refresh_token']);

        return $this->config->get(ConfigInterface::API_TOKEN);
    }

    /**
     * @param string $resourceName
     *
     * @return AbstractResource
     * @deprecated look at self::resourceFactory()
     */
    public function getResource($resourceName)
    {
        $resourceName = strtolower($resourceName);
        if (!array_key_exists($resourceName, static::$apiResources)) {
            throw new \RuntimeException("Unknown resource {$resourceName}");
        }
        $class = static::$apiResources[$resourceName];

        return new $class($this);
    }

    /**
     * @param string $lang
     */
    public function setAcceptLanguage($lang)
    {
        $this->acceptLanguage = $lang;
    }

    public function request($method, $uri, $body = [])
    {
        // Auth header
        if ($this->authType === self::AUTH_TYPE_API_KEY) {
            $headers['X-AUTH-TOKEN'] = $this->config->get(ConfigInterface::API_KEY);
        } else {
            $headers['Authorization'] = 'Bearer ' . $this->getToken();
        }
        // Content-Type header
        if ($body and strtoupper($method) !== 'GET') {
            $headers['Content-Type'] = 'application/ld+json';
        }
        // Accept language header
        if ($this->acceptLanguage) {
            $headers['Accept-Language'] = $this->acceptLanguage;
        }

        // Make request
        $response = $this->getClient()->request($method, $uri, [
            'headers' => $headers,
            'json'    => $body,
        ]);

        // Check response
        if ($response->getStatusCode() === 401) {
            $headers['Authorization'] = 'Bearer ' . $this->refreshToken();
            $response = $this->getClient()->request($method, $uri, [
                'headers' => $headers,
                'json'    => $body,
            ]);
        }

        return $response;
    }

}
