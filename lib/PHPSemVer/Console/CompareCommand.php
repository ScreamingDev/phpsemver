<?php

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\Rule;
use PHPSemVer\Rules\RuleCollection;
use PHPSemVer\Rules\RuleFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompareCommand extends AbstractCommand
{
    protected $_cacheFactory;
    protected $cacheFactory;
    /**
     * @var PHPBuilder
     */
    protected $currentBuilder  = null;
    protected $parseExceptions = array();
    /**
     * @var PHPBuilder
     */
    protected $previousBuilder = null;

    protected function configure()
    {
        $this->setName( 'compare' );

        $this->addOption(
            'type',
            't',
            InputArgument::OPTIONAL,
            'Type of given targets',
            'git'
        );

        $this->addOption(
            'ruleset',
            'R',
            InputArgument::OPTIONAL,
            'A ruleset (eg. "semver2.0")',
            'SemVer2'
        );

        $this->addArgument(
            'previous',
            InputArgument::REQUIRED,
            'Place to lookup the old code'
        );

        $this->addArgument(
            'latest',
            InputArgument::OPTIONAL,
            'Place to lookup the new code',
            'HEAD'
        );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->verbose(
            'Comparing %s "%s" with %s "%s" using "%s" ...',
            $input->getOption( 'type' ),
            $input->getArgument( 'latest' ),
            $input->getOption( 'type' ),
            $input->getArgument( 'previous' ),
            $input->getOption( 'ruleset' )
        );

        $wrapper = $this->getWrapperClass( $input->getOption( 'type' ) );

        if ( ! $wrapper )
        {
            $output->writeln(
                sprintf(
                    '<error>Unknown wrapper-type "%s"</error>',
                    $input->getOption( 'type' )
                )
            );

            return;
        }

        $previousWrapper = new $wrapper( $input->getArgument( 'previous' ) );
        $latestWrapper   = new $wrapper( $input->getArgument( 'latest' ) );

        $xmlFile        = PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/SemVer2.xml';
        $foo            = 7;
        $ruleCollection = new Rule( $xmlFile );
        $errorMessages  = $ruleCollection->processAll(
            $previousWrapper->getBuilder(),
            $latestWrapper->getBuilder()
        );

        foreach ( $errorMessages as $ruleSet => $messages )
        {
            if ( ! $messages )
            {
                continue;
            }

            $output->writeln( '' );
            $output->writeln( '' );
            $output->writeln( '<error>' . (string) $ruleSet . '</error>' );
            $output->writeln( '' );

            $table = new Table( $output );
            $table->setHeaders(
                array(
                    'Assertion',
                    'Message'
                )
            );


            foreach ( $messages as $message )
            {
                $table->addRow(
                    array(
                        $message->getRule(),
                        $message->getMessage()
                    )
                );
            }
            $table->render();
        }

        $output->writeln( 'Done!' );
    }

    public function getWrapperClass( $name )
    {
        $className = '\\PHPSemVer\\Wrapper\\' . ucfirst( $name );

        if ( ! class_exists( $className ) )
        {
            return false;
        }

        return $className;
    }
}