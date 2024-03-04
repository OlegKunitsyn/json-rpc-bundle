<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Response;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RpcNormalizationContext
{
    /**
     * @param array<string|int,mixed> $contexts
     */
    public function __construct(
        public array $contexts = [],
    ) {
    }
}
