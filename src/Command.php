<?php

/**
 * @copyright Suson Waiba. All rights reserved.
 */

namespace SusonWaiba\PhpVarnishCache;

class Command
{
    protected CliPrettyPrint $cliPrettyPrint;

    public function __construct()
    {
        $this->cliPrettyPrint = new CliPrettyPrint();
    }

    /**
     * @return array<string, mixed>
     */
    public function getMap(): array
    {
        $list = [
            'cache:clean' => [
                'description' => 'Purge cache all tags',
                'class_name' => \SusonWaiba\PhpVarnishCache\Command\CleanCommand::class,
            ],
        ];

        return $list;
    }

    public function execute(): void
    {
        $args = [];
        if (isset($_SERVER['argv'])) {
            $args = $_SERVER['argv'];
        }
        if (count($args) < 2) {
            $this->displayHelp();
            return;
        }
        if ($args[1] === 'help') {
            $this->displayHelp();
            return;
        }
        if ($args[1] === 'cache:clean') {
            $command = new \SusonWaiba\PhpVarnishCache\Command\CleanCommand();
            $command->execute();
            return;
        }

        $this->cliPrettyPrint->red('Error: Command not found.')
            ->default("Try: help")->br()->br();
    }

    protected function displayHelp(): void
    {
        $this->cliPrettyPrint
            ->orange('Usage:')->br()
            ->default('bin/php-varnish-cache command [options] [arguments]')->br()->br();
        $this->cliPrettyPrint->orange('Avilable commands:')->br();
        // These tab prints align command and description
        $this->cliPrettyPrint->green('help')
            ->default("\t\t\t\t")
            ->default('Display this help message')->br();

        foreach ($this->getMap() as $key => $item) {
            $this->cliPrettyPrint->green($key);
            if (isset($item['description'])) {
                $this->cliPrettyPrint->default("\t\t\t");
                $this->cliPrettyPrint->default($item['description']);
            }
            $this->cliPrettyPrint->br();
        }

        $this->cliPrettyPrint->br();
    }
}
