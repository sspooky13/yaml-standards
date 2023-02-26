<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Model\AbstractChecker;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check yaml file has empty line at end of file
 */
class YamlEmptyLineAtEndChecker extends AbstractChecker
{
    /**
     * @inheritDoc
     */
    public function check(string $pathToFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);

        $correctYamlLines = YamlEmptyLineAtEndDataFactory::getCorrectYamlContent($yamlLines);
        $correctYamlContent = implode("\n", $correctYamlLines);

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings, true);
    }

    /**
     * @inheritDoc
     */
    protected function runCheckYamlFileIsValid(string $pathToYamlFile): void
    {
    }
}
