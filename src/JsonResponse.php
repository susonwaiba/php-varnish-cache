<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

use GuzzleHttp\Psr7\Response;

class JsonResponse extends Response
{
    protected array $data = [];
    protected array $cacheTags = [];
    protected array $poolCodes = [];

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string> $tags
     */
    public function setCacheTags(array $tags): self
    {
        $this->cacheTags = $tags;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }

    /**
     * @param array<string> $poolCodes
     */
    public function setCachePoolCodes(array $poolCodes): self
    {
        $this->poolCodes = $poolCodes;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getCachePoolCodes(): array
    {
        return $this->poolCodes;
    }
}
