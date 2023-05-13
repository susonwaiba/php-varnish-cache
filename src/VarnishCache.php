<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class VarnishCache
{
    public function __construct(
        protected Client $client
    ) {
    }

    public function clean(string $tag = '.*'): Response
    {
        $response = $this->client->request('PURGE', '/', [
            'headers' => [
                'X-Varnish-Tag-Pattern' => $tag,
            ]
        ]);
        return $response;
    }

    public function cleanByPoolTag(string $poolCode = '.*'): Response
    {
        $response = $this->client->request('PURGE', '/', [
            'headers' => [
                'X-Varnish-Pool-Pattern' => $poolCode,
            ]
        ]);
        return $response;
    }
}
