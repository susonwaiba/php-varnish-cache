<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

use Psr\Http\Message\RequestInterface;

interface ControllerInterface
{
    public function execute(RequestInterface $request): JsonResponse;
}
