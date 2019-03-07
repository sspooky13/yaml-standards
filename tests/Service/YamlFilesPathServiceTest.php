<?php

namespace YamlStandards\Service;

use PHPUnit\Framework\TestCase;

class YamlFilesPathServiceTest extends TestCase
{
    public function testFindAllYamlFilesInDir()
    {
        $testsDir = ['./tests/yamlFiles/unSorted/'];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir, []);

        $this->assertCount(6, $yamlFiles);
    }

    public function testFindAllYamlFilesInDirExceptOfExcludedDirs()
    {
        $testsDir = ['./tests/yamlFiles/'];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir, [
            './tests/yamlFiles/sorted/config',
            './tests/yamlFiles/unSorted/route',
        ]);

        $this->assertCount(9, $yamlFiles);
    }

    public function testFindAllYamlFilesInDirExceptOfExcludedFiles()
    {
        $testsDir = ['./tests/yamlFiles/'];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir, [
            './tests/yamlFiles/sorted/config/symfony-config.yml',
            './tests/yamlFiles/unSorted/service/shopsys-service.yml',
        ]);

        $this->assertCount(10, $yamlFiles);
    }

    public function testFindAllYamlFilesInDirExceptOfExcludedDirsAndFiles()
    {
        $testsDir = ['./tests/yamlFiles/'];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir, [
            './tests/yamlFiles/sorted/config',
            './tests/yamlFiles/unSorted/route',
            './tests/yamlFiles/unSorted/service/shopsys-service.yml',
            './tests/yamlFiles/unSorted/yaml-getting-started.yml',
        ]);

        $this->assertCount(7, $yamlFiles);
    }

    public function testFindFile()
    {
        $testsFile = ['./tests/yamlFiles/sorted/yaml-getting-started.yml'];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsFile, []);

        $this->assertCount(1, $yamlFiles);
    }

    public function testReturnFullPathToFile()
    {
        $pathToFile = './tests/yamlFiles/unSorted/yaml-getting-started.yml';
        $testsFile = [$pathToFile];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsFile, []);
        $foundFile = reset($yamlFiles);

        $this->assertEquals($pathToFile, $foundFile);
    }
}
