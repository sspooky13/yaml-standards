<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceAliasing;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlServiceAliasingFixerTest extends TestCase
{
    public function testFixerFixShortFilesCorrect(): void
    {
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/shopsys-service.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/shopsys-service-short.yml',
            __DIR__ . '/resource/fixed/symfony-service-short.yml',
        ];

        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();
        $yamlServiceAliasingFixer = new YamlServiceAliasingFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT);
            $yamlServiceAliasingFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);
            $yamlFileContent = file_get_contents($tempCorrectYamlFile);
            $correctYamlFileContent = file_get_contents($pathToFixedFiles[$key]);

            $this->assertSame($correctYamlFileContent, $yamlFileContent);
        }
    }

    public function testFixerFixLongFilesCorrect(): void
    {
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/shopsys-service.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/shopsys-service-long.yml',
            __DIR__ . '/resource/fixed/symfony-service-long.yml',
        ];

        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();
        $yamlServiceAliasingFixer = new YamlServiceAliasingFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_LONG);
            $yamlServiceAliasingFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersData);
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
     * @param string $type
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(string $type): StandardParametersData
    {
        return new StandardParametersData(4, 4, 2, $type, YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, [], false);
    }
}
