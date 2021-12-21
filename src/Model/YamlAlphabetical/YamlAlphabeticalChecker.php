<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Model\AbstractChecker;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check yaml file is alphabetical sorted
 */
class YamlAlphabeticalChecker extends AbstractChecker
{
    /**
     * @inheritDoc
     */
    public function check(string $pathToFile, StandardParametersData $standardParametersData): Result
    {
        $fileContent = file_get_contents($pathToFile);
        $fileContent = str_replace("\r", '', $fileContent); // remove carriage returns

        $rightFileLines = YamlAlphabeticalDataFactory::getCorrectYamlLines($pathToFile, $standardParametersData->getDepth(), $standardParametersData->getAlphabeticalPrioritizedKeys());

        $rightFileContent = implode("\n", $rightFileLines);

        if ($fileContent === $rightFileContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($fileContent, $rightFileContent);

        return new Result($pathToFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
