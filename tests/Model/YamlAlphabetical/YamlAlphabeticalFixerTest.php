<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlAlphabeticalFixerTest extends TestCase
{
    public function testFixUnfixedFile(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
        $pathToUnfixedFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';
        $pathToFixedFile = __DIR__ . '/resource/fixed/yaml-getting-started.yml';
        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();

        $yamlAlphabeticalFixer = new YamlAlphabeticalFixer();
        $yamlAlphabeticalFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);

        $yamlFileContent = file_get_contents($tempCorrectYamlFile);
        $correctYamlFileContent = file_get_contents($pathToFixedFile);

        $this->assertSame($correctYamlFileContent, $yamlFileContent);
    }

    public function testFixUnfixedFiles(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/arraysWithUnquotedColons.yml',
            __DIR__ . '/resource/unfixed/kubernetes-postgres.yml',
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
            __DIR__ . '/resource/fixed/arraysWithUnquotedColons.yml',
            __DIR__ . '/resource/fixed/kubernetes-postgres.yml',
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

        $yamlAlphabeticalFixer = new YamlAlphabeticalFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $yamlAlphabeticalFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);
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
        return new StandardParametersData(5, 4, 4, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT, YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, [], false);
    }
}
