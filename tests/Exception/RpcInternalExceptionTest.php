<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Exception;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcInternalException;
use PHPUnit\Framework\TestCase;

class RpcInternalExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $e = new RpcInternalException();

        $this->assertSame(AbstractRpcException::INTERNAL, $e->getCode());
        $this->assertSame(AbstractRpcException::MESSAGES[AbstractRpcException::INTERNAL], $e->getMessage());
    }
}
