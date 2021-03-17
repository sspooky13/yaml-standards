<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\AbstractFixer;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Fix yaml file is alphabetical sorted
 */
class YamlAlphabeticalFixer extends AbstractFixer
{
    /**
     * @inheritDoc
     */
    public function fix(string $pathToYamlFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $fileContent = file_get_contents($pathToYamlFile);
        $fileContent = str_replace("\r", '', $fileContent); // remove carriage returns

        $rightFileLines = YamlAlphabeticalDataFactory::getCorrectYamlLines($pathToYamlFile, $standardParametersData->getDepth(), $standardParametersData->getAlphabeticalPrioritizedKeys());

        $rightFileContent = implode("\n", $rightFileLines);

        if ($fileContent === $rightFileContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        file_put_contents($pathToDumpFixedFile, $rightFileContent);

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($fileContent, $rightFileContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
