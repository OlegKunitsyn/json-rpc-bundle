<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Exception;

use OlegKunitsyn\JsonRpcBundle\Exception\RpcApplicationException;
use PHPUnit\Framework\TestCase;

class RpcApplicationExceptionTest extends TestCase
{
    public function testConstructInvalidCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('application exception code should be outside range -32099 to -32000, given: -32098');

        new RpcApplicationException('app error', RpcApplicationException::CODE_RANGE[0] + 1);
    }
}
