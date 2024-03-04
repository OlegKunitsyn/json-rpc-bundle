<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Exception;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcMethodNotFoundException;
use PHPUnit\Framework\TestCase;

class RpcMethodNotFoundExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $e = new RpcMethodNotFoundException();

        $this->assertSame(AbstractRpcException::METHOD_NOT_FOUND, $e->getCode());
        $this->assertSame(AbstractRpcException::MESSAGES[AbstractRpcException::METHOD_NOT_FOUND], $e->getMessage());
    }
}
