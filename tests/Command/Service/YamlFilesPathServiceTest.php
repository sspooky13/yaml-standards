<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

use PHPUnit\Framework\TestCase;

class YamlFilesPathServiceTest extends TestCase
{
    public function testFindAllYamlFilesInDir(): void
    {
        $pathToDir = ['./tests/Command/resource/yamlFiles/unSorted/'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDir);

        $this->assertCount(6, $yamlFiles);
    }

    public function testFindAllYamlFilesInDirs(): void
    {
        $pathToDirs = [
            './tests/Command/resource/yamlFiles/sorted/config',
            './tests/Command/resource/yamlFiles/unSorted/route',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirs);

        $this->assertCount(3, $yamlFiles);
    }

    public function testFindAllYamlFilesInPaths(): void
    {
        $pathToFiles = [
            './tests/Command/resource/yamlFiles/sorted/config/symfony-config.yml',
            './tests/Command/resource/yamlFiles/sorted/config/symfony-config.yml',
            './tests/Command/resource/yamlFiles/unSorted/route/symfony-route.yml',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToFiles);

        $this->assertCount(2, $yamlFiles);
    }

    public function testFindAllYamlFilesInPathsAndDirs(): void
    {
        $pathToDirsAndFiles = [
            './tests/Command/resource/yamlFiles/sorted/config/symfony-config.yml',
            './tests/Command/resource/yamlFiles/unSorted/route/symfony-route.yml',
            './tests/Command/resource/yamlFiles/unSorted/config',
            './tests/Command/resource/yamlFiles/unSorted/service',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirsAndFiles);

        $this->assertCount(6, $yamlFiles);
    }

    public function testReturnFullPathToFile(): void
    {
        $pathToFile = ['./tests/Command/resource/yamlFiles/unSorted/yaml-getting-started.yml'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToFile);

        $this->assertEquals($pathToFile, $yamlFiles);
    }

    public function testReturnFullPathToFilesFromDir(): void
    {
        $pathToDirs = ['./tests/Command/resource/yamlFiles/unSorted/config/', './tests/Command/resource/yamlFiles/unSorted/route/'];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirs);

        $expectedYamlFiles = [
            './tests/Command/resource/yamlFiles/unSorted/config/symfony-config.yml',
            './tests/Command/resource/yamlFiles/unSorted/config/symfony-security.yml',
            './tests/Command/resource/yamlFiles/unSorted/route/symfony-route.yml',
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
