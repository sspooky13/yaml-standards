<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use YamlStandards\Model\AbstractFixer;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Fix yaml file has empty line at end of file
 */
class YamlEmptyLineAtEndFixer extends AbstractFixer
{
    /**
     * @inheritDoc
     */
    public function fix(string $pathToFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);

        $correctYamlLines = YamlEmptyLineAtEndDataFactory::getCorrectYamlContent($yamlLines);
        $correctYamlContent = implode("\n", $correctYamlLines);

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        file_put_contents($pathToDumpFixedFile, $correctYamlContent);

        $differ = new Differ(new UnifiedDiffOutputBuilder());
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToFile, Result::RESULT_CODE_FIXED_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }

    /**
     * @inheritDoc
     */
    protected function runCheckYamlFileIsValid(string $pathToYamlFile): void
    {
    }
}
