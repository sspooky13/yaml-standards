<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use YamlStandards\Model\AbstractFixer;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Fix yaml file have space between groups
 */
class YamlSpacesBetweenGroupsFixer extends AbstractFixer
{
    /**
     * @inheritDoc
     */
    public function fix(string $pathToFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $yamlContent = file_get_contents($pathToFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);
        $lastYamlElement = end($yamlLines);
        $filteredYamlLines = array_values(array_filter($yamlLines, [YamlService::class, 'isLineNotBlank']));
        $yamlSpacesBetweenGroupsDataFactory = new YamlSpacesBetweenGroupsDataFactory();

        $correctYamlContent = $yamlSpacesBetweenGroupsDataFactory->getCorrectYamlContentWithSpacesBetweenGroups($filteredYamlLines, $standardParametersData->getLevel());

        if (YamlService::isLineBlank($lastYamlElement)) {
            $correctYamlContent .= "\n";
        }

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        file_put_contents($pathToDumpFixedFile, $correctYamlContent);

        $differ = new Differ(new UnifiedDiffOutputBuilder());
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToFile, Result::RESULT_CODE_FIXED_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }
}
