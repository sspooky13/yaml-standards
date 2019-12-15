<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check yaml file has empty line at end of file
 */
class YamlEmptyLineAtEndCheckerTest extends TestCase
{
    public function testCheckUnfixedFilesIsNotCorrect(): void
    {
        $pathToFiles = [
            __DIR__ . '/resource/unfixed/symfony-config.yml',
            __DIR__ . '/resource/unfixed/symfony-route.yml',
            __DIR__ . '/resource/unfixed/symfony-security.yml',
            __DIR__ . '/resource/unfixed/symfony-service.yml',
            __DIR__ . '/resource/unfixed/yaml-getting-started.yml',
        ];
        $yamlEmptyLineAtEndChecker = new YamlEmptyLineAtEndChecker();

        foreach ($pathToFiles as $key => $pathToFile) {
            $standardParametersData = new StandardParametersData(null, null, null, null);
            $result = $yamlEmptyLineAtEndChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFixedFilesIsCorrect(): void
    {
        $pathToFiles = [
            __DIR__ . '/resource/fixed/symfony-config.yml',
            __DIR__ . '/resource/fixed/symfony-route.yml',
            __DIR__ . '/resource/fixed/symfony-security.yml',
            __DIR__ . '/resource/fixed/symfony-service.yml',
            __DIR__ . '/resource/fixed/yaml-getting-started.yml',
        ];
        $yamlEmptyLineAtEndChecker = new YamlEmptyLineAtEndChecker();

        foreach ($pathToFiles as $key => $pathToFile) {
            $standardParametersData = new StandardParametersData(null, null, null, null);
            $result = $yamlEmptyLineAtEndChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode(), sprintf('YAML empty line at end check of "%s" failed.', $pathToFile));
        }
    }
}
