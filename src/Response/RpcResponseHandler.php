<?php

declare(strict_types=1);

namespace OlegKunitsyn\JsonRpcBundle\Response;

use OlegKunitsyn\JsonRpcBundle\Request\RpcPayload;
use Symfony\Component\HttpFoundation\JsonResponse;

class RpcResponseHandler
{
    public function __invoke(RpcPayload $payload): JsonResponse
    {
        $responseContent = null;

        if ($payload->isBatch()) {
            foreach ($payload->getRpcRequests() as $rpcRequest) {
                $responseContent[] = $rpcRequest->getResponseContent();
            }
        } else {
            $rpcRequest = $payload->getRpcRequests()[0];
            $responseContent = $rpcRequest->getResponseContent();
        }

        return new JsonResponse($responseContent);
    }
}
