<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use PHPUnit\Framework\TestCase;
use YamlStandards\Command\InputSettingData;

class YamlIndentFixerTest extends TestCase
{
    public function testFixUnfixedFile()
    {
        $inputSettingData = $this->getInputSettingDataMock();
        $pathToUnfixedFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';
        $pathToFixedFile = __DIR__ . '/resource/fixed/yaml-getting-started.yml';
        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();

        $yamlIndentChecker = new YamlIndentFixer();
        $yamlIndentChecker->fix($pathToUnfixedFile, $tempCorrectYamlFile, $inputSettingData);

        $yamlFileContent = file_get_contents($tempCorrectYamlFile);
        $correctYamlFileContent = file_get_contents($pathToFixedFile);

        $this->assertSame($correctYamlFileContent, $yamlFileContent);
    }

    public function testFixUnfixedFiles()
    {
        $inputSettingData = $this->getInputSettingDataMock();
        $pathToUnfixedFiles = [
            __DIR__ . '/resource/unfixed/kustomization.yaml',
            __DIR__ . '/resource/unfixed/shopsys-service.yml',
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
        ];
        $pathToFixedFiles = [
            __DIR__ . '/resource/fixed/kustomization.yaml',
            __DIR__ . '/resource/fixed/shopsys-service.yml',
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
        ];
        $tempCorrectYamlFile = $this->getTempCorrectYamlFile();

        $yamlIndentChecker = new YamlIndentFixer();

        foreach ($pathToUnfixedFiles as $key => $pathToUnfixedFile) {
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
     * @return \YamlStandards\Command\InputSettingData|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getInputSettingDataMock()
    {
        $inputSettingDataMock = $this->createMock(InputSettingData::class);
        $inputSettingDataMock->method('getCountOfIndents')->willReturn(4);

        return $inputSettingDataMock;
    }
}
