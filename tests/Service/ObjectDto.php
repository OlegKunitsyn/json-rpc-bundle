<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Service;

class ObjectDto
{
    public function __construct(public readonly string $arg1, public readonly ?ScalarDto $arg2 = null)
    {
    }
}
