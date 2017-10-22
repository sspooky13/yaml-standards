<?php

namespace YamlAlphabeticalChecker;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

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

    public function testDifferenceInSortedFiles()
    {
        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles([__DIR__ . '/yamlFiles/sorted']);

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            $yamlDataDifference = $yamlAlphabeticalChecker->getDifference($pathToYamlFile);

            $parseFile = Yaml::parse(file_get_contents($pathToYamlFile));
            $yamlData = Yaml::dump($parseFile, 10, 2);
            $yamlDataWithWhitespace = "\n" . $yamlData . "\n";

            $this->assertSame($yamlDataWithWhitespace, $yamlDataDifference);
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

    public function testNoDifferenceInUnSortedFiles()
    {
        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles([__DIR__ . '/yamlFiles/unSorted']);

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            $yamlDataDifference = $yamlAlphabeticalChecker->getDifference($pathToYamlFile);

            $parseFile = Yaml::parse(file_get_contents($pathToYamlFile));
            $yamlData = Yaml::dump($parseFile, 10, 2);
            $yamlDataWithWhitespace = "\n" . $yamlData . "\n";

            $this->assertNotSame($yamlDataWithWhitespace, $yamlDataDifference);
        }
    }
}
