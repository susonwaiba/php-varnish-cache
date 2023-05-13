<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

use GuzzleHttp\Psr7\Request;

class Application
{
    public function execute(): void
    {
        if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] === '/index.php') {
            $this->executeRoute();
        } else {
            $this->executeCommand();
        }
    }

    protected function executeRoute(): void
    {
        $uri = '/';
        $method = 'GET';
        if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '') {
            $uri = $_SERVER['REQUEST_URI'];
        }
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== '') {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        $request = new Request($method, $uri);

        $route = new Route();
        $response = $route->execute($request);

        $this->displayResponse($response);
    }

    protected function displayResponse(JsonResponse $response): void
    {
        // TODO: if X-Varnish-Tag is not set, then bypass varnish cache
        // By default all request are cached
        $cacheTags = $response->getCacheTags();
        if (count($cacheTags) > 0) {
            header('X-Varnish-Tag: |' . implode('|', $cacheTags) . '|', true);
        }

        $cachePoolCodes = $response->getCachePoolCodes();
        if (count($cachePoolCodes) > 0) {
            header('X-Varnish-Pool: |' . implode('|', $cachePoolCodes, true) . '|', false);
        }

        header('Content-Type: application/json; charset=utf-8', false);

        header("Cache-Control: public, max-age=604800, must-revalidate", true);

        http_response_code($response->getStatusCode() ?? 200);

        echo json_encode($response->getData(), JSON_PRETTY_PRINT);
    }

    protected function executeCommand(): void
    {
        $cliPrettyPrint = new CliPrettyPrint();
        $cliPrettyPrint->white('-----------------------------------')->br();
        $cliPrettyPrint->white('|     Php Varnish Cache - CLI     |')->br();
        $cliPrettyPrint->white('-----------------------------------')->br();
        $cliPrettyPrint->br();

        $command = new Command();
        $command->execute();
    }
}
