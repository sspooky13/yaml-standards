<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use YamlStandards\Model\AbstractFixer;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\YamlServiceAliasing\YamlServiceAliasingDataFactory;
use YamlStandards\Result\Result;

/**
 * Fix service file has uniform aliasing
 */
class YamlServiceArgumentFixer extends AbstractFixer
{
    public function fix(string $pathToFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);

        if (YamlServiceAliasingDataFactory::existsServicesInHighestParent($yamlLines) === false) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        $correctYamlLines = YamlServiceArgumentDataFactory::getCorrectYamlLines($yamlLines, YamlService::getYamlData($pathToFile), $standardParametersData);
        $correctYamlContent = implode("\n", $correctYamlLines);

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        file_put_contents($pathToDumpFixedFile, $correctYamlContent);

        $differ = new Differ(new UnifiedDiffOutputBuilder());
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToFile, Result::RESULT_CODE_FIXED_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
