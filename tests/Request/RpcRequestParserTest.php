<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Request;

use OlegKunitsyn\JsonRpcBundle\Request\RpcPayload;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequestParser;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RpcRequestParserTest extends TestCase
{
    private RpcRequestParser $parser;

    protected function setUp(): void
    {
        $this->parser = new RpcRequestParser();
    }

    public function testParsePostRequest(): void
    {
        $request = Request::create(uri: '/', method: 'POST', content: json_encode([
            'jsonrpc' => '2.0',
            'method' => 'mockService.sum',
            'params' => [1, 2],
            'id' => 'test',
        ]));

        $payload = $this->parser->parse($request);

        $this->assertInstanceOf(RpcPayload::class, $payload);
        $rpcRequest = $payload->getRpcRequests()[0];

        $this->assertNull($rpcRequest->getResponse());
        $this->assertSame('sum', $rpcRequest->getMethodKey());
        $this->assertSame('mockService', $rpcRequest->getServiceKey());
        $this->assertSame([1, 2], $rpcRequest->getParams());
        $this->assertSame('test', $rpcRequest->getId());
    }

    public function testParseBadHttpMethod(): void
    {
        $request = Request::create('/', 'PUT');
        $payload = $this->parser->parse($request);
        $rpcRequest = $payload->getRpcRequests()[0];

        $this->assertInstanceOf(RpcResponseError::class, $rpcRequest->getResponse());
    }

    public function testParseBadInvalidRpcFormat(): void
    {
        $request = Request::create(uri: '/', method: 'POST', content: json_encode([
            'jsonrpc' => '2.0',
            'wrongFormat' => 'mockService->sum',
        ]));
        $payload = $this->parser->parse($request);
        $rpcRequest = $payload->getRpcRequests()[0];

        $this->assertInstanceOf(RpcResponseError::class, $rpcRequest->getResponse());
    }
}
