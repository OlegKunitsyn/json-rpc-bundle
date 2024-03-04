<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Service;

use OlegKunitsyn\JsonRpcBundle\Response\RpcNormalizationContext;
use OlegKunitsyn\JsonRpcBundle\Service\AbstractRpcService;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class MockService extends AbstractRpcService
{
    public static function getServiceKey(): string
    {
        return 'mockService';
    }

    public function echo(ScalarDto $dto): ScalarDto
    {
        return $dto;
    }

    public function sum(ScalarDto $dto): int
    {
        return $dto->arg1 + $dto->arg2;
    }

    #[RpcNormalizationContext(['test'])]
    public function empty(): \stdClass
    {
        return (object) ['prop' => 'test'];
    }

    #[RpcNormalizationContext([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public function datetime(DateTimeDto $timestamp): DateTimeDto
    {
        return $timestamp;
    }

    public function undefined(): \stdClass
    {
        return (object) ['prop' => 'test'];
    }

    /**
     * @throws \Exception
     */
    public function exception(): void
    {
        throw new \Exception('it went wrong', 99);
    }

    public function nullable(ObjectDto $dto): ObjectDto
    {
        return $dto;
    }
}
