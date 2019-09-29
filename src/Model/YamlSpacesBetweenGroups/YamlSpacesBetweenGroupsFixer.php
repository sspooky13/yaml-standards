<?php

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\FixerInterface;
use YamlStandards\Result\Result;

/**
 * Fix yaml file have space between groups
 */
class YamlSpacesBetweenGroupsFixer implements FixerInterface
{
    /**
     * @inheritDoc
     */
    public function fix($pathToYamlFile, $pathToDumpFixedFile, InputSettingData $inputSettingData)
    {
        $yamlContent = file_get_contents($pathToYamlFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);
        $lastYamlElement = end($yamlLines);
        $filteredYamlLines = array_values(array_filter($yamlLines, [YamlService::class, 'isLineNotBlank']));
        $yamlSpacesBetweenGroupsDataFactory = new YamlSpacesBetweenGroupsDataFactory();

        $correctYamlContent = $yamlSpacesBetweenGroupsDataFactory->getCorrectYamlContentWithSpacesBetweenGroups($filteredYamlLines, $inputSettingData->getLevelForCheckSpacesBetweenGroups());

        if (trim($lastYamlElement) === '') {
            $correctYamlContent .= "\n";
        }

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        file_put_contents($pathToDumpFixedFile, $correctYamlContent);

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
