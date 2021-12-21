<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Model\AbstractChecker;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check yaml file complies right count of indent
 */
class YamlIndentChecker extends AbstractChecker
{
    /**
     * @inheritDoc
     */
    public function check(string $pathToFile, StandardParametersData $standardParametersData): Result
    {
        $fileContent = file_get_contents($pathToFile);
        $fileContent = str_replace("\r", '', $fileContent); // remove carriage returns
        $fileLines = explode("\n", $fileContent);
        $yamlIndentDataFactory = new YamlIndentDataFactory();
        $rightFileLines = [];

        foreach ($fileLines as $key => $fileLine) {
            $rightFileLines[] = $yamlIndentDataFactory->getRightFileLines($fileLines, $key, $standardParametersData, $fileLine);
        }

        $rightFileContent = implode("\n", $rightFileLines);

        if ($fileContent === $rightFileContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($fileContent, $rightFileContent);

        return new Result($pathToFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings, true);
    }
}
