<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

use GuzzleHttp\Client;

class VarnishCacheManager
{
    protected array $clientOptions = [
        'base_uri' => 'http://varnish',
        'timeout' => 2.0,
    ];
    protected ?Client $client = null;
    protected ?VarnishCache $varnishCache = null;

    /**
     * @param array<int,mixed> $options
     */
    public function setClientOptions(array $options): VarnishCacheManager
    {
        $this->clientOptions = $options;

        return $this;
    }

    public function getClientOptions(): array
    {
        return $this->clientOptions;
    }

    public function getClient(bool $forcedNew = false): Client
    {
        if (!$this->client || $forcedNew) {
            $this->client = new Client($this->getClientOptions());
        }
        return $this->client;
    }

    public function getVarnishCache(bool $forcedNew = false): VarnishCache
    {
        if (!$this->varnishCache || $forcedNew) {
            $this->varnishCache = new VarnishCache($this->getClient($forcedNew));
        }
        return $this->varnishCache;
    }
}
