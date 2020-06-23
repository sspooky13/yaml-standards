<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

use PHPUnit\Framework\TestCase;

class YamlFilesPathServiceTest extends TestCase
{
    public function testFindAllYamlFilesInDirs(): void
    {
        $pathToDirs = [
            './tests/Command/resource/yamlFiles/sorted/config/*.yml',
            './tests/Command/resource/yamlFiles/unSorted/route/*.yml',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToDirs);

        $this->assertCount(3, $yamlFiles);
    }

    public function testFindAllOthersFilesInPaths(): void
    {
        $pathToFiles = [
            './tests/Command/resource/otherFiles/*.js',
            './tests/Command/resource/otherFiles/*.json',
            './tests/Command/resource/otherFiles/*.less',
            './tests/Command/resource/otherFiles/*.md',
            './tests/Command/resource/otherFiles/*.php',
            './tests/Command/resource/otherFiles/*.xml',
        ];

        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($pathToFiles);

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
        $pathToDirs = ['./tests/Command/resource/yamlFiles/unSorted/config/*.yml', './tests/Command/resource/yamlFiles/unSorted/route/*.yml'];

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
