<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceAliasing;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\FixerInterface;
use YamlStandards\Result\Result;

/**
 * Fix service file has uniform aliasing
 */
class YamlServiceAliasingFixer implements FixerInterface
{
    /**
     * @inheritDoc
     */
    public function fix(string $pathToYamlFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToYamlFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);

        if (YamlServiceAliasingDataFactory::existsServicesInHighestParent($yamlLines) === false) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        $correctYamlLines = YamlServiceAliasingDataFactory::getCorrectYamlLines($yamlLines, YamlService::getYamlData($pathToYamlFile), $standardParametersData);
        $correctYamlContent = implode("\n", $correctYamlLines);

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        file_put_contents($pathToDumpFixedFile, $correctYamlContent);

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
