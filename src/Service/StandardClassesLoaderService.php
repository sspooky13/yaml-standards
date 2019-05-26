<?php

namespace YamlStandards\Service;

use YamlStandards\Model\YamlAlphabetical\YamlAlphabeticalChecker;
use YamlStandards\Model\YamlIndent\YamlIndentChecker;
use YamlStandards\Model\YamlInline\YamlInlineChecker;
use YamlStandards\Model\YamlSpacesBetweenGroups\YamlSpacesBetweenGroupsChecker;
use YamlStandards\Command\InputSettingData;

class StandardClassesLoaderService
{
    /**
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @return \YamlStandards\Model\CheckerInterface[]
     */
    public static function getCheckerClassesByInputSettingData(InputSettingData $inputSettingData)
    {
        $checkerClasses = [];

        if ($inputSettingData->getAlphabeticalSortDepth() !== null) {
            $checkerClasses[] = new YamlAlphabeticalChecker();
        }

        if ($inputSettingData->getCountOfIndents() !== null) {
            $checkerClasses[] = new YamlIndentChecker();
        }

        if ($inputSettingData->checkInlineStandard()) {
            $checkerClasses[] = new YamlInlineChecker();
        }

        if ($inputSettingData->getLevelForCheckSpacesBetweenGroups() !== null) {
            $checkerClasses[] = new YamlSpacesBetweenGroupsChecker();
        }

        return $checkerClasses;
    }
}
