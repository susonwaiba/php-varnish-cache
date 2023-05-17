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
        http_response_code($response->getStatusCode() ?? 200);

        // Setting debug header, this will be used by varnish to add debug headers
        header('X-Varnish-Debug: true', true);

        // By default all request are cached
        $cacheTags = $response->getCacheTags();
        $cacheTags[] = 'all';
        header('X-Varnish-Tag: |' . implode('|', $cacheTags) . '|', true);

        $cachePoolCodes = $response->getCachePoolCodes();
        $cachePoolCodes[] = 'all';
        header('X-Varnish-Pool: |' . implode('|', $cachePoolCodes) . '|', false);

        $cacheControls = $response->getCacheControls();
        // This should skip cache as default behavior
        if (!isset($cacheControls['max-age'], $cacheControls['must-understand'], $cacheControls['no-store'])) {
            $cacheControls['max-age'] = 0;
            $cacheControls['must-understand'] = true;
            $cacheControls['no-store'] = true;
        }

        $cacheControlArray = [];
        foreach ($cacheControls as $key => $val) {
            if (is_bool($val)) {
                if ($val) {
                    $cacheControlArray[] = $key;
                }
            } else {
                $cacheControlArray[] = $key . '=' . $val;
            }
        }
        $cacheControlText = implode(', ', $cacheControlArray);
        header('Cache-Control: ' . $cacheControlText, true);

        header('Content-Type: application/json; charset=utf-8', false);

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
