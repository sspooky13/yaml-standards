<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlSpacesBetweenGroupsFixerTest extends TestCase
{
    public function testCheckFixedFilesIsCorrect(): void
    {
        $levels = [1, 2, 3, 3, 1];
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
        $yamlIndentChecker = new YamlSpacesBetweenGroupsFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
            $standardParametersData = $this->getStandardsParametersData($levels[$key]);
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
     * @param int $level
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(int $level): StandardParametersData
    {
        return new StandardParametersData(4, 4, $level, YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT);
    }
}
