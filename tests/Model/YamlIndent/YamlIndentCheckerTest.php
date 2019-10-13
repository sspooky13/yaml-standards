<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use PHPUnit\Framework\TestCase;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Result\Result;

class YamlIndentCheckerTest extends TestCase
{
    public function testCheckUnfixedFileIsNotCorrect(): void
    {
        $inputSettingData = $this->getInputSettingDataMock();
        $pathToFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';

        $yamlIndentChecker = new YamlIndentChecker();
        $result = $yamlIndentChecker->check($pathToFile, $inputSettingData);

        $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
    }

    public function testCheckUnfixedFilesIsNotCorrect(): void
    {
        $inputSettingData = $this->getInputSettingDataMock();
        $pathToFiles = [
            __DIR__ . '/resource/unfixed/kustomization.yaml',
            __DIR__ . '/resource/unfixed/shopsys-service.yml',
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
        ];
        $yamlIndentChecker = new YamlIndentChecker();

        foreach ($pathToFiles as $pathToFile) {
            $result = $yamlIndentChecker->check($pathToFile, $inputSettingData);
            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFixedFileIsCorrect(): void
    {
        $inputSettingData = $this->getInputSettingDataMock();
        $pathToFile = __DIR__ . '/resource/fixed/yaml-getting-started.yml';

        $yamlIndentChecker = new YamlIndentChecker();
        $result = $yamlIndentChecker->check($pathToFile, $inputSettingData);

        $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
    }

    public function testCheckFixedFilesIsCorrect(): void
    {
        $inputSettingData = $this->getInputSettingDataMock();
        $pathToFiles = [
            __DIR__ . '/resource/fixed/kustomization.yaml',
            __DIR__ . '/resource/fixed/shopsys-service.yml',
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
        ];
        $yamlIndentChecker = new YamlIndentChecker();

        foreach ($pathToFiles as $pathToFile) {
            $result = $yamlIndentChecker->check($pathToFile, $inputSettingData);
            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode(), sprintf('YAML indent check of "%s" failed.', $pathToFile));
        }
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
