<?php

namespace PHPSemVer;

/**
 * Class Config
 *
 * @package PHPSemVer
 *
 */
class AbstractConfig
{
    protected $xml;

    protected $attributes = [];

    function __construct(\SimpleXMLElement $nodes)
    {
        $this->xml = $nodes;

        foreach ($nodes->attributes() as $name => $value) {
            $this->attributes[$name] = (string)$value;
        }
    }

    public function __call($method, $arguments)
    {
        $modifier = substr($method, 0, 3);
        $target   = substr($method, 3);

        switch ($modifier) {
        case 'get':
            $attribute = lcfirst($target);

            if ( ! isset($this->attributes[$attribute])) {
                return null;
            }

            return $this->attributes[$attribute];
            break;
        }

        $className = __CLASS__ . '\\' . ucfirst($method);

        $node = $this->getXml()->xpath($target);

        if ( ! class_exists($className)) {
            throw new \DomainException(
                sprintf(
                    'Class "%s" not found.',
                    $className
                )
            );
        }

        if (class_exists($className . 'Collection')) {
            $className .= 'Collection';

            return new $className($node);
        }

        return new $className(current($node));

    }

    /**
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }
}