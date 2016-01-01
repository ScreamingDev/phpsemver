<?php


namespace PHPSemVer\Config;


class RuleSetTest extends \PHPUnit_Framework_TestCase
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
        $xml = simplexml_load_file(__DIR__ . '/full.xml');

        return (array)$xml->xpath(RuleSet::XPATH);
    }

    /**
     * @param \SimpleXMLElement $ruleSetNode
     *
     * @dataProvider dataFullNodes
     */
    public function testItConvertsAttributesToProperties($ruleSetNode)
    {
        $ruleSet = new RuleSet($ruleSetNode);

        $this->assertNotEmpty($ruleSet->getName());
        foreach ($ruleSetNode->attributes() as $key => $value) {
            $method = 'get' . ucfirst($key);
            $this->assertEquals((string)$value, $ruleSet->$method());
        }
    }

    /**
     * @param \SimpleXMLElement $ruleSetNode
     *
     * @dataProvider dataFullNodes
     */
    public function testItContainsErrorMessages($ruleSetNode)
    {
        $ruleSet = new RuleSet($ruleSetNode);

        $exception = uniqid('err');

        $ruleSet->appendErrorMessage($exception);

        $this->assertEquals([$exception], $ruleSet->getErrorMessages());
    }
}
