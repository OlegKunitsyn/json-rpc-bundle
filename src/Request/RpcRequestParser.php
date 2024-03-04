<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Request;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcInvalidRequestException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcParseException;
use OlegKunitsyn\JsonRpcBundle\Request\RpcRequest as RpcRequestObject;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface as OptionResolverException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class RpcRequestParser
{
    private Serializer $serializer;

    private OptionsResolver $rpcResolver;

    public function __construct()
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()]);
        $this->rpcResolver = (new OptionsResolver())
            ->setRequired(['jsonrpc', 'method'])
            ->setDefined(['id'])
            ->setDefined(['params'])
            ->setAllowedValues('jsonrpc', RpcRequestObject::JSON_RPC_VERSION)
            ->setAllowedTypes('method', 'string')
            ->setAllowedValues('method', fn ($value) => 1 === preg_match('/^\w+\.\w+$/', $value))
            ->setAllowedTypes('id', ['string', 'int'])
            ->setAllowedTypes('params', ['array']);
    }

    public function parse(Request $request): RpcPayload
    {
        try {
            $data = $this->getContent($request);

            return $this->getRpcPayload($data);
        } catch (AbstractRpcException $e) {
            return $this->getRpcPayloadError($e);
        }
    }

    /**
     * @throws RpcParseException
     * @throws RpcInvalidRequestException
     */
    private function getContent(Request $request): mixed
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->getPostData($request);
        }

        throw new RpcInvalidRequestException();
    }

    /**
     * @throws RpcParseException
     */
    private function getPostData(Request $request): mixed
    {
        $data = json_decode((string) $request->getContent(), true);

        if (null === $data) {
            throw new RpcParseException();
        }

        return $data;
    }

    private function getRpcPayloadError(AbstractRpcException $e): RpcPayload
    {
        $payload = new RpcPayload();
        $rpcRequest = new RpcRequestObject();
        $rpcRequest->setResponse(new RpcResponseError($e));
        $payload->addRpcRequest($rpcRequest);

        return $payload;
    }

    /**
     * @param array<string|int,mixed> $data
     *
     * @throws RpcInvalidRequestException
     */
    private function getRpcPayload(array $data): RpcPayload
    {
        $payload = new RpcPayload();

        if (array_is_list($data)) {
            $payload->setIsBatch(true);

            foreach ($data as $subData) {
                $payload->addRpcRequest($this->getRpcRequest((array) $subData));
            }
        } else {
            $payload->addRpcRequest($this->getRpcRequest($data));
        }

        return $payload;
    }

    /**
     * @param array<string|int,mixed> $data
     *
     * @throws RpcInvalidRequestException
     */
    private function getRpcRequest(array $data): RpcRequestObject
    {
        try {
            /** @var RpcRequestObject $rpcRequest */
            $rpcRequest = $this->serializer->denormalize($data, RpcRequestObject::class);
        } catch (ExceptionInterface|\TypeError) {
            throw new RpcInvalidRequestException();
        }

        try {
            $this->rpcResolver->resolve($data);
        } catch (OptionResolverException) {
            $rpcRequest->setResponse(new RpcResponseError(new RpcInvalidRequestException(), $rpcRequest->getId()));

            return $rpcRequest;
        }

        [$serviceKey, $methodKey] = explode('.', $rpcRequest->getMethod());
        $rpcRequest->setServiceKey($serviceKey);
        $rpcRequest->setMethodKey($methodKey);

        return $rpcRequest;
    }
}
