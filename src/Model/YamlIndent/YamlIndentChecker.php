<?php

namespace YamlStandards\Model\YamlIndent;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\CheckerInterface;
use YamlStandards\Result\Result;

/**
 * Check yaml file complies right count of indent
 */
class YamlIndentChecker implements CheckerInterface
{
    /**
     * @inheritDoc
     */
    public function check($pathToYamlFile, InputSettingData $inputSettingData)
    {
        $fileContent = file_get_contents($pathToYamlFile);
        $fileContent = str_replace("\r", '', $fileContent); // remove carriage returns
        $fileLines = explode("\n", $fileContent);
        $yamlIndentDataFactory = new YamlIndentDataFactory();
        $rightFileLines = [];

        foreach ($fileLines as $key => $fileLine) {
            $rightFileLines[] = $yamlIndentDataFactory->getRightFileLines($fileLines, $key, $inputSettingData->getCountOfIndents(), $fileLine);
        }

        $rightFileContent = implode("\n", $rightFileLines);

        if ($fileContent === $rightFileContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($fileContent, $rightFileContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings, true);
    }
}
