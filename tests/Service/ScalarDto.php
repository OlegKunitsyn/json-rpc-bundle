<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Service;

class ScalarDto
{
    public function __construct(public readonly int $arg1, public readonly int $arg2)
    {
    }
}
