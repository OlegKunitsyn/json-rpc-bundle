<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Request;

use OlegKunitsyn\JsonRpcBundle\Exception\AbstractRpcException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcApplicationException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcDataExceptionInterface;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcInvalidParamsException;
use OlegKunitsyn\JsonRpcBundle\Exception\RpcMethodNotFoundException;
use OlegKunitsyn\JsonRpcBundle\Response\RpcNormalizationContext;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponse;
use OlegKunitsyn\JsonRpcBundle\Response\RpcResponseError;
use OlegKunitsyn\JsonRpcBundle\Service\ServiceFinder;
use PHPStan\BetterReflection\Reflection\ReflectionAttribute;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

class RpcRequestHandler
{
    public function __construct(
        private readonly ServiceFinder $serviceFinder,
        private readonly Serializer $serializer,
    ) {
    }

    public function handle(RpcRequest $rpcRequest): void
    {
        if ($rpcRequest->getResponse()) {
            return;
        }

        try {
            $result = $this->execute($rpcRequest);
            $rpcRequest->setResponse(new RpcResponse($result, $rpcRequest->getId()));
        } catch (\Throwable $e) {
            $e = $this->castToRpcException($e);
            $rpcRequest->setResponse(new RpcResponseError($e, $rpcRequest->getId()));
        }
    }

    /**
     * @throws ExceptionInterface
     * @throws RpcInvalidParamsException
     * @throws RpcMethodNotFoundException
     */
    private function execute(RpcRequest $rpcRequest): mixed
    {
        $serviceDescriptor = $this->serviceFinder->find($rpcRequest);
        [$service, $method, $type] = [$serviceDescriptor->getService(), $serviceDescriptor->getMethodName(), $serviceDescriptor->getMethodType()];

        $params = $rpcRequest->getParams() ?? [];
        if (!empty($params)) {
            if (null === $type) {
                throw new RpcInvalidParamsException('No object type defined');
            }

            // JSON-RPC By Position
            try {
                if (array_is_list($params)) {
                    $constructorParameters = (new \ReflectionClass($type))->getConstructor()->getParameters();
                    $keys = [];
                    foreach ($constructorParameters as $parameter) {
                        $keys[] = $parameter->getName();
                    }
                    $params = array_combine($keys, $params);
                }
                $params = $this->serializer->denormalize($params, $type);
            } catch (\Exception|\ValueError) {
                throw new RpcInvalidParamsException();
            }
        }

        $result = $service->$method($params);

        /** @var ReflectionAttribute|null $normalizationConfig */
        $normalizationConfig = $serviceDescriptor->getMethodAttribute(RpcNormalizationContext::class);
        $contexts = $normalizationConfig?->getArguments()[0] ?? [];

        return $this->serializer->normalize($result, null, $contexts);
    }

    private function castToRpcException(\Throwable $e): AbstractRpcException
    {
        if ($e instanceof AbstractRpcException) {
            return $e;
        }

        $rpcException = new RpcApplicationException($e->getMessage(), $e->getCode(), $e);

        if ($e instanceof RpcDataExceptionInterface) {
            $rpcException->setData($e->getData());
        }

        return $rpcException;
    }
}
