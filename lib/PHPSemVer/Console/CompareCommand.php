<?php

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Parser\ParserException;
use PDepend\Util\Cache\CacheFactory;
use PDepend\Util\Configuration;
use PHPSemVer\Compare\BuilderCompare;
use PHPSemVer\Wrapper\AbstractWrapper;
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

        $latestBuilder   = $this->getBuilder( $latestWrapper );
        $previousBuilder = $this->getBuilder( $previousWrapper );


        $output->writeln( 'Done!' );
    }

    /**
     * @param AbstractWrapper $latestWrapper
     *
     * @return PHPBuilder
     */
    public function getBuilder( $latestWrapper )
    {
        $builder   = new PHPBuilder();
        $tokenizer = new PHPTokenizerInternal();

        $cache = $this->getCache( uniqid() );

        foreach ( $latestWrapper->getAllFileNames() as $fileName )
        {
            if ( ! preg_match( '/\.php$/i', $fileName ) )
            {
                continue;
            }


            $tokenizer->setSourceFile( $latestWrapper->getPath( $fileName ) );

            $parser = new PHPParserGeneric( $tokenizer, $builder, $cache );

            $parser->setMaxNestingLevel( 200 );

            try
            {
                $parser->parse();
            } catch ( ParserException $e )
            {
                $this->parseExceptions[ ] = $e;
            }
        }

        exit;

        return $builder;
    }

    public function getCache( $key = null )
    {
        return $this->getCacheFactory()->create( $key );
    }

    /**
     * @return CacheFactory
     */
    public function getCacheFactory()
    {
        if ( ! $this->_cacheFactory )
        {
            $settings                = new \stdClass();
            $settings->cache         = new \stdClass();
            $settings->cache->driver = 'memory';
            $config                  = new Configuration( $settings );

            $this->_cacheFactory = new CacheFactory( $config );
        }

        return $this->_cacheFactory;
    }
}