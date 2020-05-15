<?php

namespace EpgClient\Tests\Fixtures;

use EpgClient\ConfigInterface;

class DummyConfig implements ConfigInterface
{
    /** @var array */
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function set($name, $value)
    {
        $this->config[$name] = $value;
    }
}
