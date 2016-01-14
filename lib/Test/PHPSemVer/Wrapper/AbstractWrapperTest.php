<?php

namespace Test\PHPSemVer\Wrapper;


use PHPSemVer\Wrapper\AbstractWrapper;
use Test\Abstract_TestCase;

class AbstractWrapperTest extends Abstract_TestCase {
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testItThrowsExceptionIfBaseIsNotGiven()
    {
        new AbstractWrapperTest_Subject(null);
    }
}

class AbstractWrapperTest_Subject extends AbstractWrapper {
	public function getBasePath()
	{
		return $this->getBase();
	}

	protected function fetchFileNames()
	{
		$this->fileNames = [];
	}
}