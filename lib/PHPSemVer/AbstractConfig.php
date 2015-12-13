<?php
/**
 * Contains config template.
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


namespace PHPSemVer;

/**
 * Abstract config.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class AbstractConfig
{
    protected $attributes = [];
    protected $callBuffer = [];
    protected $xml;

    function __construct(\SimpleXMLElement $nodes)
    {
        $this->xml = $nodes;

        foreach ($nodes->attributes() as $name => $value) {
            $this->attributes[$name] = (string) $value;
        }
    }

    public function __call($method, $arguments)
    {
        $modifier = substr($method, 0, 3);
        $target   = substr($method, 3);

        switch ($modifier) {
            case 'get':
                $attribute = lcfirst($target);

                if ( ! isset( $this->attributes[$attribute] )) {
                    return null;
                }

                return $this->attributes[$attribute];
                break;
        }

        $className = get_class($this).'\\'.ucfirst($method);

        $node = $this->getXml()->xpath('./'.ucfirst($method));

        if ( ! class_exists($className)) {
            throw new \DomainException(
                sprintf(
                    'Class "%s" not found.',
                    $className
                )
            );
        }

        if ( ! $node) {
            return null;
        }

        if (class_exists($className.'Collection')) {
            $className .= 'Collection';

            return new $className($node);
        }

        return new $className(current($node));
    }

    /**
     * Receive the wrapped SimpleXMLElement.
     *
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }
}