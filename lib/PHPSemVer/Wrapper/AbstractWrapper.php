<?php

namespace PHPSemVer\Wrapper;

use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Parser\ParserException;
use PDepend\Util\Cache\CacheFactory;
use PDepend\Util\Configuration;

abstract class AbstractWrapper
{
    protected $_base;
    protected $_cacheFactory;
    protected $_parserExceptions;

    public function __construct( $base )
    {
        if ( ! $base )
        {
            throw new \InvalidArgumentException( 'Please provide a base. Can not be empty.' );
        }
        $this->_base = $base;
    }

    public function getAllPaths()
    {
        $allPaths = array();

        foreach ( $this->getAllFileNames() as $fileName )
        {
            $allPaths[ $fileName ] = $this->getBasePath() . $fileName;
        }

        return $allPaths;
    }

    abstract public function getAllFileNames();

    abstract public function getBasePath();

    /**
     * @return mixed
     */
    public function getBase()
    {
        return $this->_base;
    }

    public function getBuilder()
    {
        $builder   = new PHPBuilder();
        $tokenizer = new PHPTokenizerInternal();

        $cache = $this->_getCache( uniqid() );

        foreach ( $this->getAllFileNames() as $fileName )
        {
            if ( ! preg_match( '/\.php$/i', $fileName ) )
            {
                continue;
            }

            $sourceFile = $this->getPath( $fileName );
            $tokenizer->setSourceFile( $sourceFile );

            $parser = $this->getParser( $tokenizer, $builder, $cache );

            $parser->setMaxNestingLevel( 200 );

            try
            {
                $parser->parse();
            } catch ( ParserException $e )
            {
                $this->_parserExceptions[ ] = $e;
            }
        }

        return $builder;
    }

    protected function _getCache( $key = null )
    {
        return $this->_getCacheFactory()->create( $key );
    }

    /**
     * @return CacheFactory
     */
    protected function _getCacheFactory()
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

    /**
     * @param $tokenizer
     * @param $builder
     * @param $cache
     *
     * @return PHPParserGeneric
     */
    public function getParser( $tokenizer, $builder, $cache )
    {
        return new PHPParserGeneric( $tokenizer, $builder, $cache );
    }

    /**
     * @return mixed
     */
    public function getParserExceptions()
    {
        return $this->_parserExceptions;
    }
}