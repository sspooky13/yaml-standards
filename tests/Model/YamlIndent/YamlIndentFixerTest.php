<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;

class YamlIndentFixerTest extends TestCase
{
    public function testFixUnfixedFile(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
        $pathToUnfixedFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';
        $pathToFixedFile = __DIR__ . '/resource/fixed/yaml-getting-started.yml';
        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();

        $yamlIndentChecker = new YamlIndentFixer();
        $yamlIndentChecker->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);

        $yamlFileContent = file_get_contents($tempCorrectYamlFile);
        $correctYamlFileContent = file_get_contents($pathToFixedFile);

        $this->assertSame($correctYamlFileContent, $yamlFileContent);
    }

    public function testFixUnfixedFiles(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/kustomization.yaml',
            __DIR__ . '/resource/unfixed/shopsys-service.yml',
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
            __DIR__ . '/resource/unfixed/yaml-standards.yaml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/kustomization.yaml',
            __DIR__ . '/resource/fixed/shopsys-service.yml',
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
            __DIR__ . '/resource/fixed/yaml-standards.yaml',
        ];
        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();

        $yamlIndentChecker = new YamlIndentFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $yamlIndentChecker->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);
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

    /**
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(): StandardParametersData
    {
        return new StandardParametersData(4, 4, 4, null);
    }
}
