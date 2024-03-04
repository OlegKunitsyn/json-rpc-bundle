<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Exception;

use OlegKunitsyn\JsonRpcBundle\Exception\RpcInvalidRequestException;
use PHPUnit\Framework\TestCase;

class AbstractRpcExceptionTest extends TestCase
{
    public function testConstructSpecificMessage(): void
    {
        $e = new RpcInvalidRequestException('custom');

        $this->assertSame('custom', $e->getMessage());
    }
}
