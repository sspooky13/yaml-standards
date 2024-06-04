<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use YamlStandards\Model\AbstractChecker;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\YamlServiceAliasing\YamlServiceAliasingDataFactory;
use YamlStandards\Result\Result;

/**
 * Check service file observe uniform aliasing
 */
class YamlServiceArgumentChecker extends AbstractChecker
{
    public function check(string $pathToFile, StandardParametersData $standardParametersData): Result
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

        $differ = new Differ(new UnifiedDiffOutputBuilder());
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings, true);
    }
}
