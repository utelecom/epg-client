<?php

namespace EpgClient;

class ResourceFactory
{
    /** @var array */
    private $knownResources;
    /** @var Client */
    private $client;

    public function __construct(array $knownResources, Client $client)
    {
        $this->knownResources = $knownResources;
        $this->client = $client;
    }

    public function build($resourceName)
    {
        $resourceName = strtolower($resourceName);
        if (!array_key_exists($resourceName, $this->knownResources)) {
            throw new \RuntimeException("Unknown resource {$resourceName}");
        }
        $class = $this->knownResources[$resourceName];

        return new $class($this->client);
    }
}
