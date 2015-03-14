<?php

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\Semver2\Major\NamespaceRules\NoneDeletedRule;
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

        $xmlFile = PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/SemVer2.xml';
        $xmlFileShort = trim( str_replace( getcwd(), '', $xmlFile ), DIRECTORY_SEPARATOR );

        $xml = simplexml_load_file( $xmlFile );

        $ruleSets = $xml->xpath( '//ruleset' );

        $appliedRules = array();
        foreach ( $ruleSets as $ruleSet )
        {
            $tableRows = array();
            foreach ( $ruleSet->xpath( 'rule' ) as $rule )
            {
                if ( ! $rule->attributes() || ! $rule->attributes()->ref )
                {
                    continue;
                }

                $rule = (string) $rule->attributes()->ref;
                $segments = explode( '.', $rule );
                $class    = '\\PHPSemVer\\Rules\\' . implode( '\\', $segments ) . 'Rule';

                if ( ! class_exists( $class ) )
                {
                    throw new \Exception(
                        sprintf(
                            'Invalid rule "%s" in "%s" (class "%s" not found).',
                            $rule,
                            $xmlFileShort,
                            $class
                        )
                    );

                    continue;
                }

                $singleRule = new $class( $previousWrapper->getBuilder(), $latestWrapper->getBuilder() );
                $singleRule->process();

                foreach ( $singleRule->getErrors() as $error )
                {
                    $tableRows[ ] = array(
                        $error->getRule(),
                        $error->getMessage()
                    );
                }
            }

            if ( ! $tableRows )
            {
                continue;
            }

            $table = new Table( $output );
            $table->setHeaders(
                array(
                    'Type',
                    'Message'
                )
            );

            $output->writeln( '' );
            $output->writeln( '' );
            $output->writeln( '<error>' . (string) $ruleSet->attributes()->name . '</error>' );
            $output->writeln( '' );

            $table->addRows( $tableRows );
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