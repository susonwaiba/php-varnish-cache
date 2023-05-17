<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache\Controller;

use Psr\Http\Message\RequestInterface;
use SusonWaiba\PhpVarnishCache\ControllerInterface;
use SusonWaiba\PhpVarnishCache\JsonResponse;
use SusonWaiba\PhpVarnishCache\VarnishCacheManager;

class CleanController implements ControllerInterface
{
    public function execute(RequestInterface $request): JsonResponse
    {
        $manager = new VarnishCacheManager();
        $response = $manager->getVarnishCache()->clean();

        $message = 'Cache not cleared';
        if ($response->getStatusCode() === 200) {
            $message = 'Cache cleared';
        }

        $data = [
            'message' => $message,
            'varnish_request_status_code' => $response->getStatusCode(),
        ];

        return (new JsonResponse())->setData($data);
    }
}
