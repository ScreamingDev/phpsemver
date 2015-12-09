<?php

namespace PHPSemVer;

/**
 * Class Config
 *
 * @package PHPSemVer
 *
 * @method getRuleSet()
 */
class Config extends \SimpleXMLElement
{
    public function __call($method, $arguments)
    {
        $modifier = substr($method, 0, 3);
        $target   = substr($method, 3);

        switch ($modifier) {
        case 'get':
            $className = __CLASS__ . '\\' . $target;

            if ( ! class_exists($className)) {
                throw new \DomainException(
                    sprintf(
                        'Class "%s" not found.',
                        $className
                    )
                );
            }
        }

        throw new \DomainException(
            sprintf(
                '"%s" not implemented yet',
                $method
            )
        );
    }
}