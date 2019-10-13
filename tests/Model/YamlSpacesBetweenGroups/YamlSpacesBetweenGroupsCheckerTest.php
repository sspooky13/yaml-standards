<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use PHPUnit\Framework\TestCase;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

class YamlSpacesBetweenGroupsCheckerTest extends TestCase
{
    public function testCheckUnfixedFilesIsNotCorrect(): void
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
            $standardParametersData = $this->getStandardsParametersData($levels[$key]);
            $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_INVALID_FILE_SYNTAX, $result->getResultCode());
        }
    }

    public function testCheckFixedFilesIsCorrect(): void
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
            $standardParametersData = $this->getStandardsParametersData($levels[$key]);
            $result = $yamlIndentChecker->check($pathToFile, $standardParametersData);

            $this->assertSame(Result::RESULT_CODE_OK, $result->getResultCode(), sprintf('YAML spaces between groups check of "%s" failed.', $pathToFile));
        }
    }

    /**
     * @param int $level
     * @return \YamlStandards\Model\Config\StandardParametersData|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getStandardsParametersData(int $level)
    {
        return new StandardParametersData(4, 4, $level);
    }
}
