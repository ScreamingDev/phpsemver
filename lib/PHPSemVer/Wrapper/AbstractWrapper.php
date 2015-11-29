<?php

namespace PHPSemVer\Wrapper;

use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Parser\ParserException;
use PDepend\Util\Cache\CacheFactory;
use PDepend\Util\Configuration;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;
use PHPSemVer\DataTree\DataNode;
use PHPSemVer\DataTree\Importer\NikicParser;

abstract class AbstractWrapper
{
    protected $_base;
    protected $_cacheFactory;
    protected $_parserExceptions;
    protected $excludePattern;

    public function __construct($base)
    {
        if ( ! $base) {
            throw new \InvalidArgumentException(
                'Please provide a base. Can not be empty.'
            );
        }
        $this->_base = $base;
    }

    public function getAllPaths()
    {
        $allPaths = array();

        foreach ($this->getAllFileNames() as $fileName) {
            $allPaths[$fileName] = $this->getBasePath().$fileName;
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

    public function getDataTree()
    {
        ini_set('xdebug.max_nesting_level', 3000);

        $parser = new Parser(new Emulative);

        $translator = new NikicParser();
        $dataTree   = new DataNode();

        foreach ($this->getAllFileNames() as $sourceFile) {
            if ( ! preg_match('/\.php$/i', $sourceFile)) {
                continue;
            }

            $sourceFile = realpath($sourceFile);

            $translator->importStmts(
                $parser->parse(file_get_contents($sourceFile)),
                $dataTree
            );
        }

        return $dataTree;
    }

    /**
     * @param $tokenizer
     * @param $builder
     * @param $cache
     *
     * @return PHPParserGeneric
     */
    public function getParser($tokenizer, $builder, $cache)
    {
        return new PHPParserGeneric($tokenizer, $builder, $cache);
    }

    /**
     * @return mixed
     */
    public function getParserExceptions()
    {
        return $this->_parserExceptions;
    }

    public function setExcludePattern($pattern)
    {
        $this->excludePattern = $pattern;
    }

    public function getExcludePattern()
    {
        return (array) $this->excludePattern;
    }

    protected function _getCache($key = null)
    {
        return $this->_getCacheFactory()->create($key);
    }

    /**
     * @return CacheFactory
     */
    protected function _getCacheFactory()
    {
        if ( ! $this->_cacheFactory) {
            $settings                = new \stdClass();
            $settings->cache         = new \stdClass();
            $settings->cache->driver = 'memory';
            $config                  = new Configuration($settings);

            $this->_cacheFactory = new CacheFactory($config);
        }

        return $this->_cacheFactory;
    }
}