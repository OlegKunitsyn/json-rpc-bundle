<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Exception;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcInvalidParamsException;
use PHPUnit\Framework\TestCase;

class RpcInvalidParamsExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $e = new RpcInvalidParamsException();

        $this->assertSame(AbstractRpcException::INVALID_PARAMS, $e->getCode());
        $this->assertSame(AbstractRpcException::MESSAGES[AbstractRpcException::INVALID_PARAMS], $e->getMessage());
    }
}
