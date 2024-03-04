<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Service;

abstract class AbstractRpcService
{
    abstract public static function getServiceKey(): string;
}
