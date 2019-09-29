<?php

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use YamlStandards\Model\Component\YamlService;

class YamlSpacesBetweenGroupsDataFactoryTest extends TestCase
{
    public function testGetCorrectYamlContentWithSpaces()
    {
        $yamlLines = ['foo:', '    bar: baz', '    qux: quux', '        quuz: corge', '            grault:', '                garply: waldo', '                fred: plugh'];
        $rightYamlLines = implode("\n", ['foo:', '    bar: baz', '', '    qux: quux', '        quuz: corge', '            grault:', '                garply: waldo', '', '                fred: plugh']);

        $yamlSpacesBetweenGroupsDataFactory = new YamlSpacesBetweenGroupsDataFactory();
        $yamlContent = $yamlSpacesBetweenGroupsDataFactory->getCorrectYamlContentWithSpacesBetweenGroups($yamlLines, 5);

        $this->assertSame($rightYamlLines, $yamlContent);
    }

    public function testGetCorrectLevelsFromYamlFile()
    {
        $reflectionClass = new ReflectionClass(YamlSpacesBetweenGroupsDataFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('getLevelOfCurrentLine');
        $reflectionMethod->setAccessible(true);

        $yamlLines = ['foo:', '    bar: baz', '    qux: quux', '        quuz: corge', '            grault:', '                garply: waldo'];
        $rightLevels = [1, 2, 2, 3, 4, 5];

        $yamlLineLevels = [];
        foreach ($yamlLines as $key => $yamlLine) {
            $yamlLineLevels[] = $reflectionMethod->invokeArgs(new YamlSpacesBetweenGroupsDataFactory(), [$key, $yamlLines, YamlService::rowIndentsOf($yamlLine)]);
        }

        $this->assertSame($rightLevels, $yamlLineLevels);
    }
}
