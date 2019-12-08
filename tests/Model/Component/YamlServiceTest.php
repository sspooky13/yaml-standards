<?php

declare(strict_types=1);

namespace YamlStandards\Model\Component;

use PHPUnit\Framework\TestCase;

class YamlServiceTest extends TestCase
{
    public function testLineHasValue(): void
    {
        $yamlLine = 'foo: bar';
        $hasLineValue = YamlService::hasLineValue($yamlLine);

        $this->assertTrue($hasLineValue);
    }

    public function testLineDoesNotHaveValue(): void
    {
        $yamlLine = 'foo: ';
        $hasLineValue = YamlService::hasLineValue($yamlLine);

        $this->assertFalse($hasLineValue);
    }
}
