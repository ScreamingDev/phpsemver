<?php


namespace Test\PHPUnit\PHPSemVer\Config;


use PHPSemVer\Config\Filter;
use Test\Abstract_TestCase;

class FilterTest extends Abstract_TestCase
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

    /**
     * @param $filterNode
     *
     * @dataProvider dataFullNodes
     */
    public function testItMatchesFilesAgainstPattern($filterNode)
    {
        $filter = new Filter($filterNode);

        $this->assertTrue($filter->matches('lib/Foo.php'));
        $this->assertTrue($filter->matches('pattern-test/whitelist/Foo.php'));
        $this->assertTrue($filter->matches('bin/phpsemver'));

        $this->assertFalse($filter->matches('lib/Test/Foo.php'));
        $this->assertFalse($filter->matches('pattern-test/blacklist/bla'));
        $this->assertFalse($filter->matches('somthing/very/different'));
    }
}
