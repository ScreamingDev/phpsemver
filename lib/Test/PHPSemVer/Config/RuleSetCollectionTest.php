<?php


namespace PHPSemVer\Config;


use PHPSemVer\Config;

class RuleSetCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testItIsInConfig()
    {
        $this->markTestIncomplete('not valid yet');
        return;

        $collection = $this->getFullCollection();

        $this->assertInstanceOf('\\PHPSemVer\\Config\\RuleSetCollection', $collection);
    }

    public function testCanIterateOverFullConfig()
    {
        $this->markTestIncomplete('not valid yet');
        return;

        $collection = $this->getFullCollection();

        $this->assertInstanceOf('\\PHPSemVer\\Config\\RuleSetCollection', $collection);

        $i = 0;
        foreach ($collection->getChildren() as $ruleSet) {
            $i++;
            $this->assertInstanceOf('\\PHPSemVer\\Config\\RuleSet', $ruleSet);
        }

        $this->assertGreaterThan(0, $i);

        $this->markTestIncomplete('One RuleSet might be missing (compare count and xml).');
    }

    /**
     * @return RuleSetCollection
     */
    protected function getFullCollection()
    {
        $config = new Config(simplexml_load_file(__DIR__ . '/full.xml'));

        $collection = $config->getRuleSet();

        return $collection;
    }
}
