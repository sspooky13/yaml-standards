<?php

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use YamlStandards\Command\InputSettingData;

class YamlSpacesBetweenGroupsFixerTest extends TestCase
{
    public function testCheckFixedFilesIsCorrect()
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
            $inputSettingData = $this->getInputSettingDataMock($levels[$key]);
            $yamlIndentChecker->fix($pathToUnfixedFile, $tempCorrectYamlFile, $inputSettingData);
            $yamlFileContent = file_get_contents($tempCorrectYamlFile);
            $correctYamlFileContent = file_get_contents($pathToFixedFiles[$key]);

            $this->assertSame($correctYamlFileContent, $yamlFileContent);
        }
    }

    /**
     * @return string
     */
    private function getTempCorrectYamlFile()
    {
        return __DIR__ . '/resource/temp/noName.yml';
    }

    /**
     * @param int $level
     * @return \YamlStandards\Command\InputSettingData|PHPUnit_Framework_MockObject_MockObject
     */
    private function getInputSettingDataMock($level)
    {
        $inputSettingDataMock = $this->createMock(InputSettingData::class);
        $inputSettingDataMock->method('getLevelForCheckSpacesBetweenGroups')->willReturn($level);

        return $inputSettingDataMock;
    }
}
