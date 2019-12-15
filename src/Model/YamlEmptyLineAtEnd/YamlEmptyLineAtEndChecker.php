<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\CheckerInterface;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check yaml file has empty line at end of file
 */
class YamlEmptyLineAtEndChecker implements CheckerInterface
{
    /**
     * @inheritDoc
     */
    public function check(string $pathToYamlFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToYamlFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);

        $correctYamlLines = YamlEmptyLineAtEndDataFactory::getCorrectYamlContent($yamlLines);
        $correctYamlContent = implode("\n", $correctYamlLines);

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings, true);
    }
}
