<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Response;

use OlegKunitsyn\JsonRpcBundle\Exception\RpcApplicationException;
use OlegKunitsyn\JsonRpcBundle\Request\RpcPayload;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequest;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponse;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseError;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseHandler;
use PHPUnit\Framework\TestCase;

class RpcResponderTest extends TestCase
{
    private RpcResponseHandler $responder;

    protected function setUp(): void
    {
        $this->responder = new RpcResponseHandler();
    }

    /**
     * @dataProvider provideRpcPayload
     */
    public function testResponderBatch(RpcPayload $payload, array $expected): void
    {
        $jsonResponse = ($this->responder)($payload);

        $this->assertSame($expected, json_decode($jsonResponse->getContent(), true));
    }

    public function provideRpcPayload(): \Generator
    {
        $successRequest = new RpcRequest();
        $successRequest->setResponse(new RpcResponse('success', 1));

        $errorRequest = new RpcRequest();
        $errorException = new RpcApplicationException('error', 99);
        $errorException->setData(['details']);
        $errorRequest->setResponse(new RpcResponseError($errorException, 2));

        $payload = new RpcPayload();
        $payload->setIsBatch(true);
        $payload->addRpcRequest($successRequest);
        $payload->addRpcRequest($errorRequest);

        yield [$payload,
            [
                [
                    'jsonrpc' => RpcRequest::JSON_RPC_VERSION,
                    'result' => 'success',
                    'id' => 1,
                ],
                [
                    'jsonrpc' => RpcRequest::JSON_RPC_VERSION,
                    'error' => [
                        'code' => 99,
                        'message' => 'error',
                        'data' => ['details'],
                    ],
                    'id' => 2,
                ],
            ],
        ];

        $payload = new RpcPayload();
        $payload->addRpcRequest($successRequest);

        yield [$payload, [
            'jsonrpc' => RpcRequest::JSON_RPC_VERSION,
            'result' => 'success',
            'id' => 1,
        ]];
    }
}
