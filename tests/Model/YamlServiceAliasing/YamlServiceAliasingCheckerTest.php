<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceAliasing;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;
use YamlStandards\Result\Result;

class YamlServiceAliasingCheckerTest extends TestCase
{
    public function testCheckUnfixedFilesIsNotCorrect(): void
    {
        $pathToFiles = [
            __DIR__ . '/resource/unfixed/shopsys-service.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
        ];
        $yamlServiceAliasingChecker = new YamlServiceAliasingChecker();

        foreach ($pathToFiles as $pathToFile) {
            $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT);
            $result = $yamlServiceAliasingChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFilesWithoutServicesIsOk(): void
    {
        $pathToFiles = [
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
        ];
        $yamlServiceAliasingChecker = new YamlServiceAliasingChecker();

        foreach ($pathToFiles as $pathToFile) {
            $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT);
            $result = $yamlServiceAliasingChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
        }
    }

    public function testCheckShortFixedFilesIsCorrect(): void
    {
        $pathToFiles = [
            __DIR__ . '/resource/fixed/shopsys-service-short.yml',
            __DIR__ . '/resource/fixed/symfony-service-short.yml',
        ];
        $yamlServiceAliasingChecker = new YamlServiceAliasingChecker();

        foreach ($pathToFiles as $pathToFile) {
            $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT);
            $result = $yamlServiceAliasingChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
        }
    }

    public function testCheckLongFixedFilesIsCorrect(): void
    {
        $pathToFiles = [
            __DIR__ . '/resource/fixed/shopsys-service-long.yml',
            __DIR__ . '/resource/fixed/symfony-service-long.yml',
        ];
        $yamlServiceAliasingChecker = new YamlServiceAliasingChecker();

        foreach ($pathToFiles as $pathToFile) {
            $standardParametersData = $this->getStandardsParametersData(YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_LONG);
            $result = $yamlServiceAliasingChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
        }
    }

    /**
     * @param string $type
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    private function getStandardsParametersData(string $type): StandardParametersData
    {
        return new StandardParametersData(4, 4, 2, $type);
    }
}
