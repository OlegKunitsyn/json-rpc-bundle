<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Api;

use OlegKunitsyn\JsonRpcBundle\Request\RpcRequestHandler;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequestParser;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Rpc
{
    public function __construct(
        private readonly RpcRequestParser $rpcRequestParser,
        private readonly RpcRequestHandler $rpcRequestHandler,
        private readonly RpcResponseHandler $rpcResponseHandler
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $rpcPayload = $this->rpcRequestParser->parse($request);

        foreach ($rpcPayload->getRpcRequests() as $rpcRequest) {
            $this->rpcRequestHandler->handle($rpcRequest);
        }

        return ($this->rpcResponseHandler)($rpcPayload);
    }
}
