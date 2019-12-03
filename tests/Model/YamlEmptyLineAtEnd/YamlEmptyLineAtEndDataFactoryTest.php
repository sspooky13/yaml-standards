<?php

declare(strict_types = 1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use PHPUnit\Framework\TestCase;

class YamlEmptyLineAtEndDataFactoryTest extends TestCase
{
    public function testGetCorrectYamlLinesForLinesWithoutEmptyLineAtEnd(): void
    {
        $yamlLines = ['foo:', 'bar: baz', 'qux: quux', 'quuz: corge', 'grault:', 'garply: waldo', 'fred: plugh'];
        $correctYamlLines = ['foo:', 'bar: baz', 'qux: quux', 'quuz: corge', 'grault:', 'garply: waldo', 'fred: plugh', ''];

        $rightYamlLines = YamlEmptyLineAtEndDataFactory::getCorrectYamlContent($yamlLines);

        $this->assertSame($rightYamlLines, $correctYamlLines);
    }

    public function testGetCorrectYamlLinesForLinesWithMultipleEmptyLinesAtEnd(): void
    {
        $yamlLines = ['foo:', 'bar: baz', 'qux: quux', 'quuz: corge', 'grault:', 'garply: waldo', 'fred: plugh', '', '', '', ''];
        $correctYamlLines = ['foo:', 'bar: baz', 'qux: quux', 'quuz: corge', 'grault:', 'garply: waldo', 'fred: plugh', ''];

        $rightYamlLines = YamlEmptyLineAtEndDataFactory::getCorrectYamlContent($yamlLines);

        $this->assertSame($rightYamlLines, $correctYamlLines);
    }

    public function testGetCorrectYamlLinesForEmptyArrayWithMultipleEmptyLines(): void
    {
        $yamlLines = ['', '', '', ''];
        $correctYamlLines = [''];

        $rightYamlLines = YamlEmptyLineAtEndDataFactory::getCorrectYamlContent($yamlLines);

        $this->assertSame($rightYamlLines, $correctYamlLines);
    }
}
