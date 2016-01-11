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

    /**
     * @param $whitelistNode
     *
     * @dataProvider dataFullNodes
     */
    public function testItContainsAllPattern($whitelistNode)
    {
        $whitelist = new Whitelist($whitelistNode);

        $this->assertEquals(
            [
                '@^bin/phpsemver$@',
                '@^lib/.*@',
                '@^pattern-test/whitelist/.*@',
            ],
            $whitelist->getAllPattern()
        );
    }

    /**
     * @param $whitelistNode
     *
     * @dataProvider dataFullNodes
     */
    public function testItMatchesFilesAgainstPattern($whitelistNode)
    {
        $whitelist = new Whitelist($whitelistNode);

        $this->assertTrue($whitelist->matches('lib/Test/foo'));
        $this->assertFalse($whitelist->matches('yadda'));
    }
}
