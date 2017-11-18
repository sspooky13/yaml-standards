<?php

namespace YamlAlphabeticalChecker;

use PHPUnit\Framework\TestCase;

class YamlAlphabeticalCheckerTest extends TestCase
{
    public function testFilesIsSorted()
    {
        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles([__DIR__ . '/yamlFiles/sorted']);

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            $isYamlFileSorted = $yamlAlphabeticalChecker->isDataSorted($pathToYamlFile);

            $this->assertTrue($isYamlFileSorted);
        }
    }

    public function testFileIsNotSorted()
    {
        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles([__DIR__ . '/yamlFiles/unSorted']);

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            $isYamlFileSorted = $yamlAlphabeticalChecker->isDataSorted($pathToYamlFile);

            $this->assertFalse($isYamlFileSorted);
        }
    }
}
