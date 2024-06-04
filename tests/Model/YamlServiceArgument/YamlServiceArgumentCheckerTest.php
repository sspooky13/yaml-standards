<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;
use YamlStandards\Result\Result;

class YamlServiceArgumentCheckerTest extends TestCase
{
    public function testCheckUnfixedFilesAreNotCorrect(): void
    {
        $standardParametersDataGradually = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_GRADUALLY);
        $standardParametersDataSpecifically = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_SPECIFICALLY);
        $pathToFiles = [
            __DIR__ . '/resource/unfixed/simple-service.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
        ];
        $yamlServiceArgumentChecker = new YamlServiceArgumentChecker();

        foreach ($pathToFiles as $pathToFile) {
            $result = $yamlServiceArgumentChecker->check($pathToFile, $standardParametersDataGradually);
            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
            $result = $yamlServiceArgumentChecker->check($pathToFile, $standardParametersDataSpecifically);
            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFixedFilesAreCorrect(): void
    {
        $standardParametersDataGradually = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_GRADUALLY);
        $standardParametersDataSpecifically = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_SPECIFICALLY);
        $pathToGraduallyFiles = [
            __DIR__ . '/resource/fixed/simple-service-gradually.yml',
            __DIR__ . '/resource/fixed/symfony-service-gradually.yml',
        ];
        $pathToSpecificallyFiles = [
            __DIR__ . '/resource/fixed/simple-service-specifically.yml',
            __DIR__ . '/resource/fixed/symfony-service-specifically.yml',
        ];
        $yamlServiceArgumentChecker = new YamlServiceArgumentChecker();

        foreach ($pathToGraduallyFiles as $pathToFile) {
            $result = $yamlServiceArgumentChecker->check($pathToFile, $standardParametersDataGradually);
            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
        }
        foreach ($pathToSpecificallyFiles as $pathToFile) {
            $result = $yamlServiceArgumentChecker->check($pathToFile, $standardParametersDataSpecifically);
            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
        }
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
