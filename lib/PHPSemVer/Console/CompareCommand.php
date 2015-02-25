<?php

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Compare\BuilderCompare;
use PHPSemVer\Wrapper\Git;
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
            'Comparing "%s" with "%s" using "%s" ...',
            $input->getArgument( 'latest' ),
            $input->getArgument( 'previous' ),
            $input->getOption( 'type' )
        );

        $latestWrapper   = new Git( $input->getArgument( 'latest' ) );
        $previousWrapper = new Git( $input->getArgument( 'previous' ) );


        $output->writeln( 'Done!' );
    }
}