<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Api;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class RpcTest extends WebTestCase
{
    private KernelBrowser $client;
    private RouterInterface $router;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = static::getContainer()->get('router');
    }

    public function testInvalidJson(): void
    {
        $this->client->request(method: 'POST', uri: $this->router->generate('json_rpc_bundle.endpoint'), content: '@');
        $expected = [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => AbstractRpcException::PARSE,
                'message' => AbstractRpcException::MESSAGES[AbstractRpcException::PARSE],
                'data' => null,
            ],
            'id' => null,
        ];
        $this->assertSame($expected, json_decode($this->client->getResponse()->getContent(), true));
    }

    /**
     * @dataProvider provideRpcRequest
     */
    public function testRpc(array $requestData, array $expected): void
    {
        $this->client->request(method: 'POST', uri: $this->router->generate('json_rpc_bundle.endpoint'), content: json_encode($requestData));

        $this->assertSame($expected, json_decode($this->client->getResponse()->getContent(), true));
    }

    public function provideRpcRequest(): \Generator
    {
        // returned object
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.echo', 'params' => ['arg1' => 1, 'arg2' => 2], 'id' => 'test'],
            ['jsonrpc' => '2.0', 'result' => ['arg1' => 1, 'arg2' => 2], 'id' => 'test'],
        ];

        // returned scalar
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.sum', 'params' => ['arg1' => 1, 'arg2' => 2], 'id' => 'test'],
            ['jsonrpc' => '2.0', 'result' => 3, 'id' => 'test'],
        ];

        // opposite parameters order
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.echo', 'params' => ['arg2' => 2, 'arg1' => 1], 'id' => 'test'],
            ['jsonrpc' => '2.0', 'result' => ['arg1' => 1, 'arg2' => 2], 'id' => 'test'],
        ];

        // excessive parameter
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.echo', 'params' => ['arg2' => 2, 'arg1' => 1, 'arg3' => 1], 'id' => 'test'],
            ['jsonrpc' => '2.0', 'result' => ['arg1' => 1, 'arg2' => 2], 'id' => 'test'],
        ];

        // batch
        yield [
            [
                ['jsonrpc' => '2.0', 'method' => 'mockService.sum', 'params' => ['arg1' => 1, 'arg2' => 2], 'id' => 'test_0'],
                ['jsonrpc' => '2.0', 'method' => 'mockService.sum', 'params' => ['arg1' => 3, 'arg2' => 4], 'id' => 'test_1'],
            ],
            [
                ['jsonrpc' => '2.0', 'result' => 3, 'id' => 'test_0'],
                ['jsonrpc' => '2.0', 'result' => 7, 'id' => 'test_1'],
            ],
        ];

        // unknown method
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.unknown', 'id' => 'test'],
            ['jsonrpc' => '2.0', 'error' => [
                'code' => AbstractRpcException::METHOD_NOT_FOUND,
                'message' => AbstractRpcException::MESSAGES[AbstractRpcException::METHOD_NOT_FOUND],
                'data' => null,
            ], 'id' => 'test'],
        ];

        // wrong parameter type
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.sum', 'params' => ['arg1' => '', 'arg2' => 2], 'id' => 'test'],
            ['jsonrpc' => '2.0', 'error' => [
                'code' => AbstractRpcException::INVALID_PARAMS,
                'message' => AbstractRpcException::MESSAGES[AbstractRpcException::INVALID_PARAMS],
                'data' => null,
            ], 'id' => 'test'],
        ];

        // wrong parameter name
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.sum', 'params' => ['wrongParam' => 1]],
            ['jsonrpc' => '2.0', 'error' => [
                'code' => AbstractRpcException::INVALID_PARAMS,
                'message' => AbstractRpcException::MESSAGES[AbstractRpcException::INVALID_PARAMS],
                'data' => null,
            ], 'id' => null],
        ];

        // exception
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.exception'],
            ['jsonrpc' => '2.0', 'error' => [
                'code' => 99,
                'message' => 'it went wrong',
                'data' => null,
            ], 'id' => null],
        ];

        // undefined parameters
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.undefined'],
            ['jsonrpc' => '2.0', 'result' => ['prop' => 'test'], 'id' => null],
        ];

        // nullable parameter
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.nullable', 'params' => ['arg1' => '', 'arg2' => null]],
            ['jsonrpc' => '2.0', 'result' => ['arg1' => '', 'arg2' => null], 'id' => null],
        ];

        // undefined nullable parameter
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.nullable', 'params' => ['arg1' => '']],
            ['jsonrpc' => '2.0', 'result' => ['arg1' => '', 'arg2' => null], 'id' => null],
        ];

        // returned normalized object
        yield [
            ['jsonrpc' => '2.0', 'method' => 'mockService.datetime', 'params' => ['timestamp' => '2012-04-23T18:25:43.511Z']],
            ['jsonrpc' => '2.0', 'result' => ['timestamp' => '2012-04-23'], 'id' => null],
        ];
    }
}
