<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Request;

use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseInterface;

class RpcRequest
{
    public const JSON_RPC_VERSION = '2.0';

    public function __construct(
        private readonly mixed $id = null,
        private readonly ?string $method = null,
        private ?string $serviceKey = null,
        private ?string $methodKey = null,
        /**
         * @var array<string,mixed>|null
         */
        private ?array $params = null,
        private ?RpcResponseInterface $response = null,
    ) {
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getServiceKey(): ?string
    {
        return $this->serviceKey;
    }

    public function setServiceKey(?string $serviceKey): void
    {
        $this->serviceKey = $serviceKey;
    }

    public function getMethodKey(): ?string
    {
        return $this->methodKey;
    }

    public function setMethodKey(?string $methodKey): void
    {
        $this->methodKey = $methodKey;
    }

    /**
     * @return array<string|int,mixed>|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param array<string,mixed>|null $params
     */
    public function setParams(?array $params): void
    {
        $this->params = $params;
    }

    public function getResponse(): ?RpcResponseInterface
    {
        return $this->response;
    }

    public function setResponse(?RpcResponseInterface $response): void
    {
        $this->response = $response;
    }

    /**
     * @return array<string,mixed>|null
     */
    public function getResponseContent(): ?array
    {
        return $this->response?->getContent();
    }
}
