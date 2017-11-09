<?php

namespace YamlAlphabeticalChecker;

use PHPUnit\Framework\TestCase;

class YamlFilesPathServiceTest extends TestCase
{
    public function testFindAllYamlFilesInDir()
    {
        $testsDir = [__DIR__];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir);

        $this->assertCount(10, $yamlFiles);
    }

    public function testFindYamlFilesInDirWithoutServices()
    {
        $testsDir = [__DIR__];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir, ['service']);

        $this->assertCount(8, $yamlFiles);
    }

    public function testFindYamlFilesInDirWithoutServicesAndRoutes()
    {
        $testsDir = [__DIR__];
        $yamlFiles = YamlFilesPathService::getPathToYamlFiles($testsDir, ['service', 'route']);

        $this->assertCount(6, $yamlFiles);
    }
}
