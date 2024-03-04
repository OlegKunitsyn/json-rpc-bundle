<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Response;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequest;

class RpcResponseError implements RpcResponseInterface
{
    public function __construct(private AbstractRpcException $rpcException, private readonly mixed $id = null)
    {
    }

    /**
     * @return array<string,mixed>
     */
    public function getContent(): array
    {
        return [
            'jsonrpc' => RpcRequest::JSON_RPC_VERSION,
            'error' => [
                'code' => $this->rpcException->getCode(),
                'message' => $this->rpcException->getMessage(),
                'data' => $this->rpcException->getData(),
            ],
            'id' => $this->id,
        ];
    }

    public function getRpcException(): AbstractRpcException
    {
        return $this->rpcException;
    }

    public function setRpcException(AbstractRpcException $rpcException): void
    {
        $this->rpcException = $rpcException;
    }
}
