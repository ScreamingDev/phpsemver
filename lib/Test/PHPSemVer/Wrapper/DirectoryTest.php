<?php

namespace Test\PHPSemVer\Wrapper;


use Symfony\Component\Filesystem\Filesystem;
use Test\Abstract_TestCase;

class DirectoryTest extends Abstract_TestCase
{
    public function testItListAllFiles()
    {
        // When I instantiate a wrapper on a direcotry
        $fileWrapper = new \PHPSemVer\Wrapper\Directory( static::BASE_DIR . '/resource/v0' );

        // And fetch all file names
        $allFileNames = $fileWrapper->getAllFileNames();
        $this->assertNotEmpty( $allFileNames );

        // Then it should contain all PHP files in that directory
        foreach ( $this->getAllFileNamesV0() as $fileName )
        {
            $this->assertArrayHasKey( $fileName, $allFileNames );
        }
    }

    public function getAllFileNamesV0()
    {
        return array(
            'RemovedClass.php',
        );
    }
}
