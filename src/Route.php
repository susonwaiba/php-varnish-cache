<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

use SusonWaiba\PhpVarnishCache\Controller\Error404Controller;
use Psr\Http\Message\RequestInterface;

class Route
{
    /**
     * @return array<string, string>
     */
    public function getMap(): array
    {
        $list = [
            '/' => \SusonWaiba\PhpVarnishCache\Controller\HomeController::class,
            '/clean' => \SusonWaiba\PhpVarnishCache\Controller\CleanController::class,
            '/404' => Error404Controller::class,
        ];

        return $list;
    }

    public function execute(RequestInterface $request): JsonResponse
    {
        $baseUri = $request->getUri()->getPath();
        try {
            if (isset($this->getMap()[$baseUri])) {
                return $this->executeController($this->getMap()[$baseUri], $request);
            } elseif (isset($this->getMap()[$request->getMethod() . ' ' . $baseUri])) {
                return $this->executeController($this->getMap()[$request->getMethod() . ' ' . $baseUri], $request);
            }
        } catch (\Exception $e) {
            return (new JsonResponse())->setData([
                'message' => $e->getMessage(),
            ])->withStatus(500);
        }

        try {
            $errorController = new Error404Controller();
            return $errorController->execute($request);
        } catch (\Exception $e) {
            return (new JsonResponse())->setData([
                'message' => $e->getMessage(),
            ])->withStatus(500);
        }
    }

    protected function executeController(string $className, RequestInterface $request): JsonResponse
    {
        if (!class_exists($className)) {
            throw new \Exception('Controller class ' . $className . ' not found');
        }
        $controller = new $className();
        if ($controller instanceof ControllerInterface) {
            return $controller->execute($request);
        }

        throw new \Exception('Controller must implement ControllerInterface');
    }
}
