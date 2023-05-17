<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache\Controller;

use Psr\Http\Message\RequestInterface;
use SusonWaiba\PhpVarnishCache\ControllerInterface;
use SusonWaiba\PhpVarnishCache\JsonResponse;

class HomeController implements ControllerInterface
{
    public function execute(RequestInterface $request): JsonResponse
    {
        $response = new JsonResponse();

        $data = [
            'message' => 'hello from home controller',
            'date-time' => date('Y-m-d H:i:s'),
        ];

        return $response->setData($data)
            ->setCacheTags(['home'])
            ->setCachePoolCodes(['page'])
            ->setCacheControls([
                'max-age' => 172800, // 60*60*24*2
            ]);
    }
}
