<?php

namespace YamlStandards\Model\YamlIndent;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\FixerInterface;
use YamlStandards\Result\Result;

/**
 * Fix yaml file with right count of indent
 */
class YamlIndentFixer implements FixerInterface
{
    /**
     * @inheritDoc
     */
    public function fix($pathToYamlFile, $pathToDumpFixedFile, InputSettingData $inputSettingData)
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

        file_put_contents($pathToDumpFixedFile, $rightFileContent);

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($fileContent, $rightFileContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
