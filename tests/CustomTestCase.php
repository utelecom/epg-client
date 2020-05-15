<?php

namespace EpgClient\Tests;

use EpgClient\Context\AbstractContext;

class CustomTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $expectedClass
     * @param AbstractContext[] $content
     */
    public function assertApiResponseCollection($expectedClass, $content)
    {
        $this->assertTrue(is_array($content));
        $item = reset($content);
        $this->assertApiResponseSingleResult($expectedClass, $item);
    }

    /**
     * @param string $expectedClass
     * @param AbstractContext $content
     */
    public function assertApiResponseSingleResult($expectedClass, $content)
    {
        $this->assertInstanceOf($expectedClass, $content);
    }
}
