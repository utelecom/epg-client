<?php

namespace EpgClient;

use EpgClient\Context\Account;
use EpgClient\Context\Category;
use EpgClient\Context\CategoryImage;
use EpgClient\Context\Channel;
use EpgClient\Context\ChannelImage;
use EpgClient\Context\Genre;
use EpgClient\Context\Provider;
use EpgClient\Resource\AbstractResource;
use EpgClient\Resource\AccountResource;
use EpgClient\Resource\CategoryImagesResource;
use EpgClient\Resource\CategoryResource;
use EpgClient\Resource\ChannelImagesResource;
use EpgClient\Resource\ChannelResource;
use EpgClient\Resource\GenreResource;
use EpgClient\Resource\ProviderResource;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 * @package EpgClient
 *
 * @method AccountResource getAccountResource()
 * @method ChannelResource getChannelResource()
 * @method ChannelImagesResource getChannelImageResource()
 * @method ProviderResource getProviderResource()
 * @method CategoryResource getCategoryResource()
 * @method CategoryImagesResource getCategoryImageResource()
 * @method GenreResource getGenreResource()
 */
class Client
{
    const LANG_UK = 'uk';
    const LANG_RU = 'ru';

    const ACCOUNT = 'account';
    const PROVIDER = 'provider';
    const CHANNEL = 'channel';
    const CHANNEL_IMAGE = 'channelimage';
    const CATEGORY = 'category';
    const CATEGORY_IMAGE = 'categoryimage';
    const GENRE = 'genre';

    private static $apiResources = [
        self::ACCOUNT        => AccountResource::class,
        self::PROVIDER       => ProviderResource::class,
        self::CHANNEL        => ChannelResource::class,
        self::CHANNEL_IMAGE  => ChannelImagesResource::class,
        self::CATEGORY       => CategoryResource::class,
        self::CATEGORY_IMAGE => CategoryImagesResource::class,
        self::GENRE          => GenreResource::class,
    ];

    private static $apiContexts = [
        self::ACCOUNT        => Account::class,
        self::PROVIDER       => Provider::class,
        self::CHANNEL        => Channel::class,
        self::CHANNEL_IMAGE  => ChannelImage::class,
        self::CATEGORY       => Category::class,
        self::CATEGORY_IMAGE => CategoryImage::class,
        self::GENRE          => Genre::class,
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

    private function resourceFactory()
    {
        if (!$this->resourceFactory) {
            $payload = $this->getTokenPayload();
            $this->resourceFactory = new ResourceFactory(static::$apiResources, $this, $payload);
        }

        return $this->resourceFactory;
    }

    public function __call($name, $arguments)
    {
        if (preg_match('#^get(.+?)Resource$#', $name, $matches)) {
            return $this->resourceFactory()->build($matches[1]);
        }

        throw new \BadMethodCallException;
    }

    /**
     * @deprecated look at self::resourceFactory()
     * @param string $resourceName
     * @return AbstractResource
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
        // Build headers
        $headers['Authorization'] = 'Bearer ' . $this->getToken();
        if ($body and strtoupper($method) !== 'GET') {
            $headers['Content-Type'] = 'application/ld+json';
        }
        $this->acceptLanguage and $headers['Accept-Language'] = $this->acceptLanguage;

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
     * @return JWTPayload
     */
    protected function getTokenPayload()
    {
        $jwt = $this->getToken();
        return new JWTPayload($jwt);
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

}
