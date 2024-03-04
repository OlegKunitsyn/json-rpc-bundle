<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Exception;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcParseException;
use PHPUnit\Framework\TestCase;

class RpcParseExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $e = new RpcParseException();

        $this->assertSame(AbstractRpcException::PARSE, $e->getCode());
        $this->assertSame(AbstractRpcException::MESSAGES[AbstractRpcException::PARSE], $e->getMessage());
    }
}
