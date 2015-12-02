<?php
/**
 * Abstract wrapper.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

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

/**
 * Basic functionality for wrapper.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
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
     * Get version.
     *
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

    public function getExcludePattern()
    {
        return (array) $this->excludePattern;
    }

    public function setExcludePattern($pattern)
    {
        $this->excludePattern = $pattern;
    }

    /**
     * Get parser for files.
     *
     * @param $tokenizer
     * @param $builder
     * @param $cache
     *
     * @deprecated 3.0.0
     *
     * @return PHPParserGeneric
     */
    public function getParser($tokenizer, $builder, $cache)
    {
        return new PHPParserGeneric($tokenizer, $builder, $cache);
    }

    /**
     * Get all errors.
     *
     * @return mixed
     */
    public function getParserExceptions()
    {
        return $this->_parserExceptions;
    }

    protected function _getCache($key = null)
    {
        return $this->_getCacheFactory()->create($key);
    }

    /**
     * CacheFactory for parser.
     *
     * @deprecated 3.0.0
     *
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