<?php

namespace EpgClient;

use EpgClient\Context\AbstractContext;
use EpgClient\Context\Channel;
use EpgClient\Context\ChannelImage;

/**
 * Class ContextFactory
 * @package EpgClient
 *
 * @method Channel createChannel()
 * @method ChannelImage createChannelImage()
 */
class ContextFactory
{
    /** @var array */
    protected $knownContexts;

    public function __construct(array $knownContexts = [])
    {
        $this->knownContexts = $knownContexts;
    }

    public function __call($name, $arguments)
    {
        if (preg_match('#^create(.+)$#', $name, $matches)) {
            $type = $matches[1];
            return $this->createEmptyContext($type);
        }

        throw new \BadMethodCallException;
    }

    /**
     * @param array $data
     * @return AbstractContext
     */
    public function createFromRaw(array $data)
    {
        $type = self::parseContextType($data);
        if (!$type || !$this->getContextClass($type)) {
            throw new \RuntimeException("Unknown context type {$type}!");
        }

        return $this->createNestedItem($data);
    }

    /**
     * @param mixed $data
     * @return false|string
     */
    protected function parseContextType($data)
    {
        return is_array($data) && isset($data['@type']) ? $data['@type'] : false;
    }

    /**
     * @param string $type
     * @return false|string
     */
    protected function getContextClass($type)
    {
        $type = strtolower(str_replace('_', '', $type));
        if (array_key_exists($type, $this->knownContexts) && class_exists($this->knownContexts[$type])) {
            return $this->knownContexts[$type];
        }

        return false;
    }

    /**
     * @param string $type
     * @return AbstractContext
     */
    private function createEmptyContext($type)
    {
        $class = $this->getContextClass($type);
        if (!$class) {
            throw new \RuntimeException("Unknown context type {$type}!");
        }

        return new $class();
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    private function createNestedItem($item)
    {
        $type = self::parseContextType($item);
        if (!$type) {
            return $item;
        }

        /** @var AbstractContext $context */
        $context = $this->createEmptyContext($type);
        foreach ($item as $property => $value) {
            if (is_array($value) && is_numeric(key($value))) {
                $context->__set($property, array_map(function ($item) {
                    return $this->createNestedItem($item);
                }, $value));
            } elseif (is_array($value)) {
                $context->__set($property, $this->createNestedItem($value));
            } else {
                $context->__set($property, $value);
            }
        }

        return $context;
    }
}
