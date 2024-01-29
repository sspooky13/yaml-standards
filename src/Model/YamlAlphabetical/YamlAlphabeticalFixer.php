<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Yaml\Yaml;
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
    public function fix(string $pathToFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $fileContent = file_get_contents($pathToFile);
        $fileContent = str_replace("\r", '', $fileContent); // remove carriage returns

        $rightFileLines = YamlAlphabeticalDataFactory::getCorrectYamlLines($pathToFile, $standardParametersData->getDepth(), $standardParametersData->getAlphabeticalPrioritizedKeys());

        $rightFileContent = implode("\n", $rightFileLines);

        if ($fileContent === $rightFileContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        file_put_contents($pathToDumpFixedFile, $rightFileContent);

        $differ = new Differ(new UnifiedDiffOutputBuilder());
        $diffBetweenStrings = $differ->diff($fileContent, $rightFileContent);

        return new Result($pathToFile, Result::RESULT_CODE_FIXED_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
