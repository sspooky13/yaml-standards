<?php

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Result\Result;

class YamlSpacesBetweenGroupsCheckerTest extends TestCase
{
    public function testCheckUnfixedFilesIsNotCorrect()
    {
        $levels = [1, 2, 3, 3, 1];
        $pathToFiles = [
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
        ];
        $yamlIndentChecker = new YamlSpacesBetweenGroupsChecker();

        foreach ($pathToFiles as $key => $pathToFile) {
            $inputSettingData = $this->getInputSettingDataMock($levels[$key]);
            $result = $yamlIndentChecker->check($pathToFile, $inputSettingData);

            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFixedFilesIsCorrect()
    {
        $levels = [1, 2, 3, 3, 1];
        $pathToFiles = [
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
        ];
        $yamlIndentChecker = new YamlSpacesBetweenGroupsChecker();

        foreach ($pathToFiles as $key => $pathToFile) {
            $inputSettingData = $this->getInputSettingDataMock($levels[$key]);
            $result = $yamlIndentChecker->check($pathToFile, $inputSettingData);

            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode(), sprintf('YAML spaces between groups check of "%s" failed.', $pathToFile));
        }
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
