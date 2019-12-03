<?php

declare(strict_types = 1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;

/**
 * Fix yaml file has empty line at end of file
 */
class YamlEmptyLineAtEndFixerTest extends TestCase
{
    public function testFixerFixFilesCorrect(): void
    {
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
        ];

        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();
        $yamlEmptyLineAtEndFixer = new YamlEmptyLineAtEndFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $standardParametersData = new StandardParametersData(null, null, null);
            $yamlEmptyLineAtEndFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);
            $yamlFileContent = file_get_contents($tempCorrectYamlFile);
            $correctYamlFileContent = file_get_contents($pathToFixedFiles[$key]);

            $this->assertSame($correctYamlFileContent, $yamlFileContent);
        }
    }

    /**
     * @return string
     */
    private function getTempCorrectYamlFile(): string
    {
        return __DIR__ . '/resource/temp/noName.yml';
    }
}
