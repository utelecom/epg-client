<?php

namespace EpgClient\Resource;

use EpgClient\Client;
use EpgClient\Context\AbstractContext;
use EpgClient\JWTPayload;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractResource
{
    /** @var string */
    protected static $baseLocation;

    /** @var Client */
    protected $client;
    /** @var string */
    protected $method;
    /** @var null|string */
    protected $location;
    /** @var null|array */
    protected $body;
    /** @var null|ResponseInterface */
    protected $response;
    /** @var array */
    protected $filters = [];
    /** @var array */
    protected $groups = [];
    /** @var array */
    private $options;
    private $acceptLanguage;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function init(JWTPayload $payload)
    {
        // Nothing here
    }

    public function get($location = null)
    {
        $this->reset();
        $this->method = 'GET';
        $this->location = $location ?: static::$baseLocation;

        return $this;
    }

    protected function reset()
    {
        $this->method = null;
        $this->location = null;
        $this->body = null;
        $this->response = null;
        $this->filters = [];
        $this->groups = [];
    }

    public function put(AbstractContext $context)
    {
        $this->reset();
        if (!$context->getLocation()) {
            throw new \RuntimeException("Unknown resource location. Maybe you wanna create new resource with POST?");
        }

        $this->method = 'PUT';
        $this->location = $context->getLocation();
        $this->body = $context;

        return $this;
    }

    public function delete(AbstractContext $context)
    {
        $this->reset();
        if (!$context->getLocation()) {
            throw new \RuntimeException("Unknown resource location. Maybe you wanna create new resource with POST?");
        }

        $this->method = 'DELETE';
        $this->location = $context->getLocation();
        $this->body = $context;

        return $this;
    }

    public function post(AbstractContext $context)
    {
        $this->reset();
        $this->method = 'POST';
        $this->location = static::$baseLocation;
        $this->body = $context;

        return $this;
    }

    /**
     * @return AbstractContext|null
     */
    public function getSingleResult()
    {
        if ($this->method === 'DELETE') {
            /** @var AbstractContext $context */
            $context = $this->body;
            return $context;
        }

        $data = $this->client->responseToArray($this->response);
        if (self::contentLookLikeCollection($data)) {
            $items = self::contentParseCollectionItems($data);
            if (count($items) > 1) {
                throw new \RuntimeException("Response collection with more than one item");
            } elseif (count($items) === 0) {
                return null;
            } else {
                $item = reset($items);
            }
        } else {
            $item = $data;
        }

        return $this->client->contextFactory()->createFromRaw($item);
    }

    private static function contentLookLikeCollection(array $data)
    {
        return isset($data['hydra:totalItems']);
    }

    private static function contentParseCollectionItems(array $data)
    {
        if (!self::contentLookLikeCollection($data)) {
            throw new \RuntimeException("Response does not look like collection");
        }

        return $data['hydra:member'];
    }

    /**
     * @return AbstractContext|false
     */
    public function getFirstResult()
    {
        $content = $this->getArrayResult();

        return current($content);
    }

    /**
     * @param array $options Allowed options: `indexBy`
     * @return AbstractContext[]
     */
    public function getArrayResult($options = [])
    {
        $data = $this->client->responseToArray($this->response);
        if (!self::contentLookLikeCollection($data)) {
            throw new \RuntimeException("Response does not look like collection");
        }

        $content = [];
        foreach (self::contentParseCollectionItems($data) as $item) {
            $context = $this->client->contextFactory()->createFromRaw($item);
            if (isset($options['indexBy']) and $context->propertyExist($options['indexBy'])) {
                $propertyValue = $context->__get($options['indexBy']);
                $content[$propertyValue] = $context;
            } else {
                $content[] = $context;
            }
        }

        return $content;
    }

    public function exec()
    {
        if (!$this->method) {
            throw new \InvalidArgumentException("HTTP `method` does not set!");
        } elseif (!$this->location) {
            throw new \InvalidArgumentException("Resource `location` does not set!");
        }

        $query = [];
        $this->options and $query += $this->options;
        $this->filters and $query += $this->filters;
        $this->groups and $query['groups'] = array_keys($this->groups);
        $this->acceptLanguage and $query['locale'] = $this->acceptLanguage;

        $location = $this->location;
        if ($query) {
            $location .= '?' . http_build_query($query);
        }

        $body = json_decode(json_encode($this->body), true);
        $this->response = $this->client->request($this->method, $location, $body);
        if (!$this->isResponseValid()) {
            $body = $this->client->responseToArray($this->response);
            throw new \RuntimeException(sprintf("Error %s: %s.\nResource %s.\nLocation:%s\nBody: %s",
                $this->response->getStatusCode(),
                $this->response->getReasonPhrase(),
                get_class($this),
                $location,
                var_export($body, true)
            ));
        }

        return $this;
    }

    protected function isResponseValid()
    {
        return
            (in_array($this->method, ['GET', 'PUT']) && $this->response->getStatusCode() === 200)
            || ($this->method === 'POST' && $this->response->getStatusCode() === 201)
            || ($this->method === 'DELETE' && $this->response->getStatusCode() === 204);
    }

    public function addFilter($property, $value)
    {
        $this->filters[$property] = $value;

        return $this;
    }

    public function withGroup($groupName)
    {
        $this->groups[$groupName] = true;

        return $this;
    }

    public function disablePagination()
    {
        $this->options['pagination'] = false;

        return $this;
    }

    public function setLanguage($lang)
    {
        $this->acceptLanguage = $lang;
    }
}
