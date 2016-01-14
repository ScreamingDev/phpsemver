<?php
/**
 * Filter Class.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */


namespace PHPSemVer\Config;

use PHPSemVer\AbstractConfig;
use PHPSemVer\Config;
use PHPSemVer\Config\Filter\Blacklist;
use PHPSemVer\Config\Filter\Whitelist;

/**
 * Config > Filter.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 *
 * @method Blacklist blacklist()
 * @method Whitelist whitelist()
 */
class Filter extends AbstractConfig
{
    const XPATH = '//phpsemver/Filter';

    public function matches($fileName)
    {
        $match = true;

        if ($this->whitelist() && $this->whitelist()->getAllPattern()) {
            $match = $this->whitelist()->matches($fileName);
        }

        if ( ! $match) {
            return $match;
        }

        if ($this->blacklist() && $this->blacklist()->getAllPattern()) {
            $match = ! $this->blacklist()->matches($fileName);
        }

        return $match;
    }
}