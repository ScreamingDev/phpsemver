<?php

namespace Test\PHPSemVer\DataTree\Importer;


use PHPSemVer\DataTree\Importer\ParentVisitor;

class ParentVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testItClearsTheStack()
    {
        $visitor = new ParentVisitor();

        $object             = new \ReflectionObject($visitor);

        $reflectionProperty = $object->getProperty('stack');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($visitor, 123);

        $visitor->beginTraverse([]);

        $this->assertEquals([], $reflectionProperty->getValue($visitor));
    }
}
