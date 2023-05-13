<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache\Controller;

use Psr\Http\Message\RequestInterface;
use SusonWaiba\PhpVarnishCache\ControllerInterface;
use SusonWaiba\PhpVarnishCache\JsonResponse;

class Error404Controller implements ControllerInterface
{
    public function execute(RequestInterface $request): JsonResponse
    {
        $response = new JsonResponse();

        $data = [
            'message' => '404 Not Found',
        ];

        return $response->setData($data)->setCacheTags(['error404'])->withStatus(404);
    }
}
