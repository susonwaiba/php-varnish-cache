<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache\Command;

use SusonWaiba\PhpVarnishCache\VarnishCacheManager;
use SusonWaiba\PhpVarnishCache\CliPrettyPrint;

class CleanCommand
{
    public function execute(): void
    {
        $cliPrettyPrint = new CliPrettyPrint();

        $manager = new VarnishCacheManager();
        $response = $manager->getVarnishCache()->clean();

        if ($response->getStatusCode() === 200) {
            $cliPrettyPrint->green('Success: Cache cleared')->br()->br();
        } else {
            $cliPrettyPrint->red('Error: Cache not cleared')->br()->br();
        }
    }
}
