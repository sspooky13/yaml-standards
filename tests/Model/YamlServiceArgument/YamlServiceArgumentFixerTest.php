<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlServiceArgumentFixerTest extends TestCase
{
    public function testFixerFixFilesCorrect(): void
    {
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/simple-service.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
        ];
        $pathToFixedGraduallyFiles = [
            __DIR__ . '/resource/fixed/simple-service-gradually.yml',
            __DIR__ . '/resource/fixed/symfony-service-gradually.yml',
        ];
        $pathToFixedSpecificallyFiles = [
            __DIR__ . '/resource/fixed/simple-service-specifically.yml',
            __DIR__ . '/resource/fixed/symfony-service-specifically.yml',
        ];

        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();
        $YamlServiceArgumentFixer = new YamlServiceArgumentFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $standardParametersDataGradually = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_GRADUALLY);
            $YamlServiceArgumentFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersDataGradually);
            $yamlFileContent = file_get_contents($tempCorrectYamlFile);
            $correctYamlFileContent = file_get_contents($pathToFixedGraduallyFiles[$key]);
            $this->assertSame($correctYamlFileContent, $yamlFileContent);

            $standardParametersDataSpecifically = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_SPECIFICALLY);
            $YamlServiceArgumentFixer->fix($pathToUnfixedFile, $tempCorrectYamlFile, $standardParametersDataSpecifically);
            $yamlFileContent = file_get_contents($tempCorrectYamlFile);
            $correctYamlFileContent = file_get_contents($pathToFixedSpecificallyFiles[$key]);
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
     * @param string $argumentsType
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(string $argumentsType): StandardParametersData
    {
        return new StandardParametersData(4, 4, 4, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT, YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_DEFAULT, [], false, $argumentsType);
    }
}
