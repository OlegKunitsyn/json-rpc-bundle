<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Exception;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcInvalidRequestException;
use PHPUnit\Framework\TestCase;

class RpcInvalidRequestExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $e = new RpcInvalidRequestException();

        $this->assertSame(AbstractRpcException::INVALID_REQUEST, $e->getCode());
        $this->assertSame(AbstractRpcException::MESSAGES[AbstractRpcException::INVALID_REQUEST], $e->getMessage());
    }
}
