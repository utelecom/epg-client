<?php

namespace EpgClient\Context;

use JsonSerializable;

abstract class AbstractContext implements JsonSerializable
{
    /** @var array */
    protected $data = [];

    public function jsonSerialize()
    {
        return $this->data;
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        if ($value && $value instanceof self) {
            $value = $value->getLocation();
        }

        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function getLocation()
    {
        return $this->data['@id'];
    }

    public function getType()
    {
        return $this->data['@type'];
    }

    public function isNew()
    {
        return !$this->getLocation();
    }

    public function propertyExist($property)
    {
        return array_key_exists($property, $this->data);
    }
}
