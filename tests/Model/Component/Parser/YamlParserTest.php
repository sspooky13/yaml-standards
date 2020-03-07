<?php

declare(strict_types=1);

namespace YamlStandards\Model\Component\Parser;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class YamlParserTest extends TestCase
{
    public function testGetRightCountOfParents(): void
    {
        $groupOfYamlLines = [
            ['- foo:', '        - bar'],
            ['- foo:', '        - bar', 'baz: qux'],
            ['- foo:', '        - bar', '    baz:', '        qux: quux'],
            ['- foo:', '        - bar', '        - baz', '  qux:', '        - quux'],
            ['- foo:', '        - bar', '  baz:', '        - qux'],
            ['- foo:', '        - bar', '          baz:', '              - qux'],
            ['foo:', '    bar: baz'],
            ['foo:', '    - bar: baz'],
            ['foo:', '    bar:', '        baz: quux'],
            ['foo:', '- bar'],
            ['foo:', '- bar:', '            - baz'],
            ['- foo:', '    bar: baz', '- qux:', '        - quux: quuz'],
        ];
        $keyToCheck = [1, 1, 3, 2, 3, 3, 1, 1, 2, 1, 2, 2];
        $rightCountOfParents = [2, 2, 2, 2, 2, 3, 1, 1, 2, 1, 3, 0];

        foreach ($groupOfYamlLines as $key => $yamlLines) {
            $countOfParents = YamlParser::getCountOfParentsForLine($yamlLines, $keyToCheck[$key]);

            $this->assertSame($rightCountOfParents[$key], $countOfParents, sprintf('`%s` has wrong count of parents', implode(',', $yamlLines)));
        }
    }

    public function testIsPrevLineNextParent(): void
    {
        $reflectionClass = new ReflectionClass(YamlParser::class);
        $reflectionMethod = $reflectionClass->getMethod('isPrevLineNextParent');
        $reflectionMethod->setAccessible(true);

        $isNextParent = $reflectionMethod->invokeArgs(null, [false, 2, 4, false]);
        $this->assertTrue($isNextParent);

        $isNextParent = $reflectionMethod->invokeArgs(null, [true, 2, 4, false]);
        $this->assertTrue($isNextParent);

        $isNextParent = $reflectionMethod->invokeArgs(null, [true, 2, 4, true]);
        $this->assertTrue($isNextParent);
    }

    public function testIsClassicHierarchy(): void
    {
        $reflectionClass = new ReflectionClass(YamlParser::class);
        $reflectionMethod = $reflectionClass->getMethod('isClassicHierarchy');
        $reflectionMethod->setAccessible(true);

        $isNextParent = $reflectionMethod->invokeArgs(null, [false, 2, 4]);
        $this->assertTrue($isNextParent);
    }

    public function testIsStartOfArray(): void
    {
        $reflectionClass = new ReflectionClass(YamlParser::class);
        $reflectionMethod = $reflectionClass->getMethod('isStartOfArray');
        $reflectionMethod->setAccessible(true);

        $isNextParent = $reflectionMethod->invokeArgs(null, [true, 2, 3, false]);
        $this->assertTrue($isNextParent);
    }

    public function testIsStartOfArrayInArray(): void
    {
        $reflectionClass = new ReflectionClass(YamlParser::class);
        $reflectionMethod = $reflectionClass->getMethod('isStartOfArrayInArray');
        $reflectionMethod->setAccessible(true);

        $isNextParent = $reflectionMethod->invokeArgs(null, [true, 2, 3, true]);
        $this->assertTrue($isNextParent);
    }
}
