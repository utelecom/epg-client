<?php

namespace EpgClient;

use EpgClient\Token\JWTPayload;

class ResourceFactory
{
    /** @var array */
    private $knownResources;
    /** @var Client */
    private $client;
    /** @var JWTPayload */
    private $payload;

    public function __construct(array $knownResources, Client $client, JWTPayload $payload)
    {
        $this->knownResources = $knownResources;
        $this->client = $client;
        $this->payload = $payload;
    }

    public function build($resourceName)
    {
        $resourceName = strtolower($resourceName);
        if (!array_key_exists($resourceName, $this->knownResources)) {
            throw new \RuntimeException("Unknown resource {$resourceName}");
        }
        $class = $this->knownResources[$resourceName];
        $instance = new $class($this->client);
        $instance->init($this->payload);
        return $instance;
    }
}
