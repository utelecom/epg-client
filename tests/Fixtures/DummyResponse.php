<?php

namespace EpgClient\Tests\Fixtures;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class DummyResponse implements ResponseInterface
{

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body)
    {
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
    }

    /**
     * @inheritDoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase()
    {
    }
}
