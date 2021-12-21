<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceAliasing;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Model\AbstractChecker;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check service file observe uniform aliasing
 */
class YamlServiceAliasingChecker extends AbstractChecker
{
    /**
     * @inheritDoc
     */
    public function check(string $pathToFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);

        if (YamlServiceAliasingDataFactory::existsServicesInHighestParent($yamlLines) === false) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        $correctYamlLines = YamlServiceAliasingDataFactory::getCorrectYamlLines($yamlLines, YamlService::getYamlData($pathToFile), $standardParametersData);
        $correctYamlContent = implode("\n", $correctYamlLines);

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings, true);
    }
}
