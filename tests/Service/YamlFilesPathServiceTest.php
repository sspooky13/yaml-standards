<?php

namespace YamlStandards\Service;

use PHPUnit\Framework\TestCase;

class YamlFilesPathServiceTest extends TestCase
{
    public function testFindAllYamlFilesInDir()
    {
        $testsDir = ['./tests/yamlFiles/unSorted/'];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir);

        $this->assertCount(6, $yamlFiles);
    }

    public function testFindFile()
    {
        $testsFile = ['./tests/yamlFiles/sorted/yaml-getting-started.yml'];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsFile);

        $this->assertCount(1, $yamlFiles);
    }

    public function testReturnFullPathToFile()
    {
        $pathToFile = './tests/yamlFiles/unSorted/yaml-getting-started.yml';
        $testsFile = [$pathToFile];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsFile);
        $foundFile = reset($yamlFiles);

        $this->assertEquals($pathToFile, $foundFile);
    }
}
