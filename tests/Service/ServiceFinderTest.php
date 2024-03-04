<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Service;

use OlegKunitsyn\JsonRpcBundle\Exception\RpcMethodNotFoundException;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequest;
use OlegKunitsyn\JsonRpcBundle\Service\ServiceDescriptor;
use OlegKunitsyn\JsonRpcBundle\Service\ServiceFinder;
use PHPUnit\Framework\TestCase;

class ServiceFinderTest extends TestCase
{
    private \ArrayIterator $services;

    protected function setUp(): void
    {
        $this->services = new \ArrayIterator([
            'mockService' => new MockService(),
        ]);
    }

    /**
     * @dataProvider providePayload
     *
     * @throws RpcMethodNotFoundException
     */
    public function testFind(RpcRequest $payload, ?string $expectedResult, ?string $expectedException = null): void
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }

        $serviceLocator = new ServiceFinder($this->services);

        $this->assertInstanceOf($expectedResult, $serviceLocator->find($payload));
    }

    public function providePayload(): \Generator
    {
        $rpcRequest = new RpcRequest();
        $rpcRequest->setMethodKey('sum');
        $rpcRequest->setServiceKey('mockService');

        yield [$rpcRequest, ServiceDescriptor::class];

        $rpcRequestUnknownService = new RpcRequest();
        $rpcRequestUnknownService->setMethodKey('sum');
        $rpcRequestUnknownService->setServiceKey('unknown');

        yield [$rpcRequestUnknownService, null, RpcMethodNotFoundException::class];

        $rpcRequestUnknownMethod = new RpcRequest();
        $rpcRequestUnknownMethod->setMethodKey('unknown');
        $rpcRequestUnknownMethod->setServiceKey('mockService');

        yield [$rpcRequestUnknownMethod, null, RpcMethodNotFoundException::class];
    }
}
