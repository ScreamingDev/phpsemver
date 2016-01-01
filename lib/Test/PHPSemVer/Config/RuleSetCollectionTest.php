<?php


namespace PHPSemVer\Config;


use PHPSemVer\Config;

class RuleSetCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testItIsInConfig()
    {
        $collection = $this->getFullCollection();

        $this->assertInstanceOf('\\PHPSemVer\\Config\\RuleSetCollection', $collection);
    }

    public function testCanIterateOverFullConfig()
    {
        $collection = $this->getFullCollection();

        $this->assertInstanceOf('\\PHPSemVer\\Config\\RuleSetCollection', $collection);

        $i = 0;
        foreach ($collection->getChildren() as $ruleSet) {
            $i++;
            $this->assertInstanceOf('\\PHPSemVer\\Config\\RuleSet', $ruleSet);
        }

        $this->assertGreaterThan(0, $i);

        // Assert that all nodes have been parsed.
        $fullConfig = $this->makeFullConfig();
        $amount = 0;
        foreach ($fullConfig->getXml()->xpath('RuleSet') as $ruleSet) {
            $amount++;
        }

        $this->assertEquals($amount, $i);
    }

    /**
     * @return RuleSetCollection
     */
    protected function getFullCollection()
    {
        return $this->makeFullConfig()->ruleSet();
    }

    /**
     * @return Config
     */
    protected function makeFullConfig()
    {
        return new Config(simplexml_load_file(__DIR__.'/full.xml'));
    }
}
