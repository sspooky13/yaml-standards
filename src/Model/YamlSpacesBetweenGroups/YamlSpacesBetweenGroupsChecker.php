<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\CheckerInterface;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check yaml file have space between groups
 */
class YamlSpacesBetweenGroupsChecker implements CheckerInterface
{
    /**
     * @inheritDoc
     */
    public function check(string $pathToYamlFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToYamlFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);
        $lastYamlElement = end($yamlLines);
        $filteredYamlLines = array_values(array_filter($yamlLines, [YamlService::class, 'isLineNotBlank']));
        $yamlSpacesBetweenGroupsDataFactory = new YamlSpacesBetweenGroupsDataFactory();

        $correctYamlContent = $yamlSpacesBetweenGroupsDataFactory->getCorrectYamlContentWithSpacesBetweenGroups($filteredYamlLines, $standardParametersData->getLevel());

        if (YamlService::isLineBlank($lastYamlElement)) {
            $correctYamlContent .= "\n";
        }

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings, true);
    }
}
