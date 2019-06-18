<?php

namespace YamlStandards\Command\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class YamlFilesPathServiceTest extends TestCase
{
    public function testFindAllYamlFilesInDir()
    {
        $pathToDir = ['./tests/yamlFiles/unSorted/'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDir, new NullOutput());

        $this->assertCount(6, $yamlFiles);
    }

    public function testFindAllYamlFilesInDirs()
    {
        $pathToDirs = [
            './tests/yamlFiles/sorted/config',
            './tests/yamlFiles/unSorted/route',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirs, new NullOutput());

        $this->assertCount(3, $yamlFiles);
    }

    public function testFindAllYamlFilesInPaths()
    {
        $pathToFiles = [
            './tests/yamlFiles/sorted/config/symfony-config.yml',
            './tests/yamlFiles/unSorted/route/symfony-route.yml',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToFiles, new NullOutput());

        $this->assertCount(2, $yamlFiles);
    }

    public function testFindAllYamlFilesInPathsAndDirs()
    {
        $pathToDirsAndFiles = [
            './tests/yamlFiles/sorted/config/symfony-config.yml',
            './tests/yamlFiles/unSorted/route/symfony-route.yml',
            './tests/yamlFiles/unSorted/config',
            './tests/yamlFiles/unSorted/service',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirsAndFiles, new NullOutput());

        $this->assertCount(6, $yamlFiles);
    }

    public function testReturnFullPathToFile()
    {
        $pathToFile = ['./tests/yamlFiles/unSorted/yaml-getting-started.yml'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToFile, new NullOutput());

        $this->assertEquals($pathToFile, $yamlFiles);
    }

    public function testReturnFullPathToFilesFromDir()
    {
        $pathToDirs = ['./tests/yamlFiles/unSorted/config/', './tests/yamlFiles/unSorted/route/'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirs, new NullOutput());

        $expectedYamlFiles = [
            './tests/yamlFiles/unSorted/config/symfony-config.yml',
            './tests/yamlFiles/unSorted/config/symfony-security.yml',
            './tests/yamlFiles/unSorted/route/symfony-route.yml',
        ];

        $this->assertTrue($this->arraysAreEqual($yamlFiles, $expectedYamlFiles)); // assert two arrays are equal, but order of elements is not important
    }

    /**
     * @param string[] $expectedPaths
     * @param string[] $actualPaths
     * @return bool
     */
    private function arraysAreEqual(array $expectedPaths, array $actualPaths)
    {
        $differentBetweenArrays = array_diff($actualPaths, $expectedPaths);
        if (count($differentBetweenArrays) > 0) {
            return false;
        }

        if (count($actualPaths) !== count($expectedPaths)) {
            return false;
        }

        foreach ($actualPaths as $actualPath) {
            if (in_array($actualPath, $expectedPaths, true) === false) {
                return false;
            }
        }

        return true;
    }
}
