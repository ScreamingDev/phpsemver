<?php

namespace Test\PHPSemVer\Wrapper;


use PHPSemVer\Config\Filter;
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

    public function testItExcludeFilesByRegExpPattern()
    {
        // When I instantiate a wrapper on a direcotry
        $fileWrapper = new \PHPSemVer\Wrapper\Directory( static::BASE_DIR . '/resource/v0' );

        $blacklistMock = $this->getMock('\\PHPSemVer\\Config\\Filter\\Blacklist', ['getAllPattern'], [], '', false);
        $blacklistMock->expects($this->any())
                      ->method('getAllPattern')
                      ->willReturn(
                          [
                              '@ignoreMe@',
                          ]
                      );

        $filterMock = $this->getMock('\\PHPSemVer\\Config\\Filter', ['blacklist'], [], '', false);

        $filterMock->expects($this->any())
                   ->method('blacklist')
                   ->willReturn($blacklistMock);

        // And ignore all files containing "ignoreMe"
        $fileWrapper->setFilter($filterMock);

        // And fetch all file names
        $allFileNames = $fileWrapper->getAllFileNames();
        $this->assertNotEmpty( $allFileNames );



        foreach ( $this->getAllFileNamesV0() as $fileName )
        {
            // Then it should not have the ignored files
            if (false !== strpos($fileName, 'ignoreMe')) {
                $this->assertArrayNotHasKey($fileName, $allFileNames);
                continue;
            }

            // And contain all other files
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
