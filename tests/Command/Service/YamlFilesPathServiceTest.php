<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

use PHPUnit\Framework\TestCase;

class YamlFilesPathServiceTest extends TestCase
{
    public function testFindAllYamlFilesInDir(): void
    {
        $pathToDir = ['./tests/yamlFiles/unSorted/'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDir);

        $this->assertCount(6, $yamlFiles);
    }

    public function testFindAllYamlFilesInDirs(): void
    {
        $pathToDirs = [
            './tests/yamlFiles/sorted/config',
            './tests/yamlFiles/unSorted/route',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirs);

        $this->assertCount(3, $yamlFiles);
    }

    public function testFindAllYamlFilesInPaths(): void
    {
        $pathToFiles = [
            './tests/yamlFiles/sorted/config/symfony-config.yml',
            './tests/yamlFiles/unSorted/route/symfony-route.yml',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToFiles);

        $this->assertCount(2, $yamlFiles);
    }

    public function testFindAllYamlFilesInPathsAndDirs(): void
    {
        $pathToDirsAndFiles = [
            './tests/yamlFiles/sorted/config/symfony-config.yml',
            './tests/yamlFiles/unSorted/route/symfony-route.yml',
            './tests/yamlFiles/unSorted/config',
            './tests/yamlFiles/unSorted/service',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirsAndFiles);

        $this->assertCount(6, $yamlFiles);
    }

    public function testReturnFullPathToFile(): void
    {
        $pathToFile = ['./tests/yamlFiles/unSorted/yaml-getting-started.yml'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToFile);

        $this->assertEquals($pathToFile, $yamlFiles);
    }

    public function testReturnFullPathToFilesFromDir(): void
    {
        $pathToDirs = ['./tests/yamlFiles/unSorted/config/', './tests/yamlFiles/unSorted/route/'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirs);

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
    private function arraysAreEqual(array $expectedPaths, array $actualPaths): bool
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
