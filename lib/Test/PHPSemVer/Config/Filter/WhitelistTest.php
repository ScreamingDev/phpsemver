<?php


namespace Test\PHPUnit\PHPSemVer\Config\Filter;


use PHPSemVer\Config\Filter;
use PHPSemVer\Config\Filter\Whitelist;
use Test\Abstract_TestCase;

class WhitelistTest extends Abstract_TestCase
{
    public function dataFullNodes()
    {

        $dataProvider = [];
        foreach ($this->getFullNodes() as $node) {
            $dataProvider[] = [$node];
        }

        return $dataProvider;
    }

    public function getFullNodes()
    {
        $xml = simplexml_load_file($this->getResourcePath('Rules/FullSpec.xml'));

        return (array) $xml->xpath(Whitelist::XPATH);
    }

    /**
     * @param $filterNode
     *
     * @dataProvider dataFullNodes()
     */
    public function testItHasOneChild($filterNode)
    {
        $filter = new Whitelist($filterNode);

        $this->assertEquals(3, $filter->getXml()->count());
    }
}
