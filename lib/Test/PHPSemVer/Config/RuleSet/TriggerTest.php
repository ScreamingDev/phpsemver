<?php


namespace PHPSemVer\Config\RuleSet;


use PHPSemVer\Config\RuleSet;

class TriggerTest extends \PHPUnit_Framework_TestCase
{
    public function dataAllTrigger()
    {
        $dataAllTrigger = [
            'first' => [
                [
                    'Classes/IsRemoved',
                ],
            ],
        ];

        $xml = simplexml_load_file(__DIR__ . '/../full.xml');

        foreach ($xml->xpath(RuleSet::XPATH) as $ruleSetNode) {
            $ruleSet = new RuleSet($ruleSetNode);

            $current = current($ruleSetNode->xpath('Trigger'));

            if ( ! $current) {
                continue;
            }

            array_unshift(
                $dataAllTrigger[$ruleSet->getName()],
                new Trigger($current)
            );
        }

        return $dataAllTrigger;
    }

    /**
     * @dataProvider dataAllTrigger
     */
    public function testGetAllTrigger($trigger, $innerClasses)
    {
        $allTrigger = $trigger->getAll();

        sort($allTrigger);
        sort($innerClasses);

        $overflow = array_diff($allTrigger, $innerClasses);
        if ($overflow) {
            $this->markTestIncomplete(
                sprintf(
                    'Missing: %s',
                    implode(', ', $overflow)
                )
            );
        }

        $underflow = array_diff($innerClasses, $allTrigger);

        $this->assertEmpty(
            $underflow,
            sprintf(
                'Could not find %s',
                implode(', ', $underflow)
            )
        );
    }
}
