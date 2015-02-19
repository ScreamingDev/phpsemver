<?php

namespace Semver\Console;

use Symfony\Component\Console\Input\InputOption;

class Application extends \Symfony\Component\Console\Application
{
    public function fetchCommands()
    {
        $Directory = new \RecursiveDirectoryIterator(__DIR__);
        $Iterator  = new \RecursiveIteratorIterator($Directory);

        // try to find all commands
        $Regex = new \RegexIterator(
            $Iterator,
            '@.*Command.php@',
            \RecursiveRegexIterator::GET_MATCH
        );

        // add if is command
        foreach ($Regex as $single) {
            $file = preg_replace(
                '@' . preg_quote(SEMVER_LIB_PATH, '@') . '@',
                '',
                $single[0],
                1
            );


            // skip abstracts
            if (false !== strpos($file, '/Abstract')) {
                continue;
            }

            $class = str_replace(DIRECTORY_SEPARATOR, '\\', dirname($file))
                     . '\\' . basename($file, '.php');

            // skip false positives
            if (!class_exists($class)) {
                continue;
            }

            $this->add(new $class);
        }
    }

    public function __construct()
    {
        parent::__construct('Softec', '1.0.0');

        $definition = $this->getDefinition();

        $definition->addOption(
            new InputOption(
                'target',
                't',
                InputOption::VALUE_OPTIONAL,
                'File or folder to check',
                '.'
            )
        );


        $this->setDefinition($definition);
    }
}