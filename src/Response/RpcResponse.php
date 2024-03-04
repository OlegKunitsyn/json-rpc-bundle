<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Response;

use OlegKunitsyn\JsonRpcBundle\Request\RpcRequest;

class RpcResponse implements RpcResponseInterface
{
    public function __construct(private readonly mixed $data, private readonly mixed $id = null)
    {
    }

    /**
     * @return array<string,mixed>
     */
    public function getContent(): array
    {
        return [
            'jsonrpc' => RpcRequest::JSON_RPC_VERSION,
            'result' => $this->data,
            'id' => $this->id,
        ];
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
