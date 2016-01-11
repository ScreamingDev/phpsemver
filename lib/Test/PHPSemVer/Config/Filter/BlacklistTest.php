<?php


namespace Test\PHPUnit\PHPSemVer\Config\Filter;


use PHPSemVer\Config\Filter;
use PHPSemVer\Config\Filter\Blacklist;
use Test\Abstract_TestCase;

class BlacklistTest extends Abstract_TestCase
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

        return (array) $xml->xpath(Blacklist::XPATH);
    }

    /**
     * @param $filterNode
     *
     * @dataProvider dataFullNodes()
     */
    public function testItHasOneChild($filterNode)
    {
        $filter = new Blacklist($filterNode);

        $this->assertEquals(2, $filter->getXml()->count());
    }

    /**
     * @param $blacklistNode
     *
     * @dataProvider dataFullNodes
     */
    public function testItContainsAllPattern($blacklistNode)
    {
        $blacklist = new Blacklist($blacklistNode);

        $this->assertEquals(
            [
                '@^pattern-test/blacklist/.*@',
                '@^lib/Test/.*@',
            ],
            $blacklist->getAllPattern()
        );
    }

    /**
     * @param $blacklistNode
     *
     * @dataProvider dataFullNodes
     */
    public function testItMatchesFilesAgainstPattern($blacklistNode)
    {
        $blacklist = new Blacklist($blacklistNode);

        $this->assertTrue($blacklist->matches('lib/Test/foo'));
        $this->assertFalse($blacklist->matches('./lib/Test/foo'));
    }
}
