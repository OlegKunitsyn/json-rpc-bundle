<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Service;

use OlegKunitsyn\JsonRpcBundle\Exception\RpcMethodNotFoundException;

class ServiceDescriptor
{
    private \ReflectionMethod $methodReflection;

    /**
     * @throws RpcMethodNotFoundException
     */
    public function __construct(private readonly object $service, string $method)
    {
        try {
            $this->methodReflection = new \ReflectionMethod($service::class, $method);
        } catch (\ReflectionException) {
            throw new RpcMethodNotFoundException();
        }
    }

    public function getMethodName(): string
    {
        return $this->methodReflection->getName();
    }

    public function getService(): object
    {
        return $this->service;
    }

    public function getServiceClass(): string
    {
        return $this->service::class;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return \ReflectionAttribute<T>|null
     */
    public function getMethodAttribute(string $class): ?\ReflectionAttribute
    {
        return $this->methodReflection->getAttributes($class)[0] ?? null;
    }

    /**
     * @return class-string|null
     */
    public function getMethodType(): ?string
    {
        foreach ($this->methodReflection->getParameters() as $parameter) {
            /** @var \ReflectionNamedType|\ReflectionUnionType|\ReflectionIntersectionType|null $parameterType */
            $parameterType = $parameter->getType();
            if (null !== $parameterType && !$parameterType->isBuiltin()) {
                return $parameterType->getName();
            }
        }

        return null;
    }
}
