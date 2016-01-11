<?php


namespace Test\PHPUnit\PHPSemVer\Config;


use PHPSemVer\Config\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase
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
        $xml = simplexml_load_file(__DIR__.'/full.xml');

        return (array) $xml->xpath(Filter::XPATH);
    }

    /**
     * @param $filterNode
     *
     * @dataProvider dataFullNodes()
     */
    public function testItHasTwoChildren($filterNode)
    {
        $filter = new Filter($filterNode);

        $this->assertEquals(2, $filter->getXml()->count());
    }
}
