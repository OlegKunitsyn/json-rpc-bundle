<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Tests\Service;

class DateTimeDto
{
    public function __construct(public readonly \DateTimeImmutable $timestamp)
    {
    }
}
