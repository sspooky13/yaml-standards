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
}
