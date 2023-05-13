<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

class CliPrettyPrint
{
    public function br(): self
    {
        echo PHP_EOL;

        return $this;
    }

    public function default(string $text): self
    {
        echo "\033[39m" . $text . "\033[0m";

        return $this;
    }

    public function red(string $text): self
    {
        echo "\033[91m" . $text . "\033[0m";

        return $this;
    }

    public function green(string $text): self
    {
        echo "\033[92m" . $text . "\033[0m";

        return $this;
    }

    public function orange(string $text): self
    {
        echo "\033[93m" . $text . "\033[0m";

        return $this;
    }

    public function blue(string $text): self
    {
        echo "\033[94m" . $text . "\033[0m";

        return $this;
    }

    public function white(string $text): self
    {
        echo "\033[97m" . $text . "\033[0m";

        return $this;
    }
}
