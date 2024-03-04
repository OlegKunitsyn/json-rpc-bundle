<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Request;

use OlegKunitsyn\JsonRpcBundle\Exception\RpcApplicationException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcInvalidRequestException;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequest;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequestHandler;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseError;
use OlegKunitsyn\JsonRpcBundle\Service\ServiceFinder;
use OlegKunitsyn\JsonRpcBundle\Tests\Service\MockService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class RpcRequestHandlerTest extends TestCase
{
    private RpcRequestHandler $requestHandler;

    private Serializer|MockObject $serializer;

    protected function setUp(): void
    {
        $services = new \ArrayIterator(['mockService' => new MockService()]);
        $this->serializer = $this->createMock(Serializer::class);

        $serviceFinder = new ServiceFinder($services);
        $this->requestHandler = new RpcRequestHandler($serviceFinder, $this->serializer);
    }

    /**
     * @dataProvider provideRpcRequest
     *
     * @param null $expectedResult
     */
    public function testHandle(RpcRequest $rpcRequest, $expectedResult = null, ?RpcResponseError $expectedError = null): void
    {
        if ($expectedError) {
            $this->assertSame($expectedResult, $rpcRequest->getResponse());
        } else {
            $this->serializer->expects($this->once())->method('normalize')->with($expectedResult, null, []);
        }

        $this->requestHandler->handle($rpcRequest);
    }

    public function testNormalizationContext(): void
    {
        $rpcRequest = new RpcRequest(serviceKey: 'mockService', methodKey: 'empty');

        $this->serializer->expects($this->once())->method('normalize')
            ->with($this->isInstanceOf(\stdClass::class), null, ['test']);

        $this->requestHandler->handle($rpcRequest);
    }

    public function provideRpcRequest(): \Generator
    {
        $badTypeRpcRequest = new RpcRequest(serviceKey: 'mockService', methodKey: 'sum');
        $badTypeRpcRequest->setParams(['arg1' => '5', 'arg2' => 5]);

        yield [$badTypeRpcRequest, null, new RpcResponseError(new RpcInvalidRequestException())];

        $badArgCountRpcRequest = new RpcRequest(serviceKey: 'mockService', methodKey: 'sum');
        $badArgCountRpcRequest->setParams(['arg1' => 5]);

        yield [$badArgCountRpcRequest, null, new RpcResponseError(new RpcInvalidRequestException())];

        $exceptionRpcRequest = new RpcRequest(serviceKey: 'mockService', methodKey: 'exception');

        yield [$exceptionRpcRequest, null, new RpcResponseError(new RpcApplicationException())];

        $successRpcRequest = new RpcRequest(serviceKey: 'mockService', methodKey: 'sum');
        $successRpcRequest->setParams(['arg1' => 5, 'arg2' => 5]);
    }
}
