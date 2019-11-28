<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

class YamlIndentCheckerTest extends TestCase
{
    public function testCheckUnfixedFileIsNotCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
        $pathToFile = __DIR__ . '/resource/unfixed/yaml-getting-started.yml';

        $yamlIndentChecker = new YamlIndentChecker();
        $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);

        $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
    }

    public function testCheckUnfixedFilesIsNotCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
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
            $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);
            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFixedFileIsCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
        $pathToFile = __DIR__ . '/resource/fixed/yaml-getting-started.yml';

        $yamlIndentChecker = new YamlIndentChecker();
        $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);

        $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode());
    }

    public function testCheckFixedFilesIsCorrect(): void
    {
        $standardParametersData = $this->getStandardsParametersData();
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
            $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);
            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode(), sprintf('YAML indent check of "%s" failed.', $pathToFile));
        }
    }

    /**
     * @return \YamlStandards\Model\Config\StandardParametersData|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getStandardsParametersData()
    {
        return new StandardParametersData(4, 4, 4);
    }
}
