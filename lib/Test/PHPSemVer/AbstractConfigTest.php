<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 02.01.16
 * Time: 13:05
 */

namespace Test\PHPSemVer;


use PHPSemVer\AbstractConfig;
use PHPSemVer\Config;
use Test\Abstract_TestCase;

class AbstractConfigTest extends Abstract_TestCase
{
    public function testMissingXMLNodesReturnNull()
    {
        $xmlFile = $this->getResourcePath('Rules/Empty.xml');

        $this->assertFileExists($xmlFile);

        $config = new Config(simplexml_load_file($xmlFile));

        $this->assertNull($config->ruleSet());
    }

    public function testGettingNonExistantAttributeIsNull()
    {
        $xmlFile = $this->getResourcePath('Rules/Empty.xml');

        $this->assertFileExists($xmlFile);

        $config = new Config(simplexml_load_file($xmlFile));

        $this->assertNull($config->nothingHere);
    }
}

class AbstractConfigTest_Subject extends AbstractConfig
{
}