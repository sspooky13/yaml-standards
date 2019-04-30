<?php

namespace YamlStandards\Service;

use YamlStandards\Checker\YamlAlphabeticalChecker;
use YamlStandards\Checker\YamlIndentChecker;
use YamlStandards\Checker\YamlInlineChecker;
use YamlStandards\Checker\YamlSpacesBetweenGroupsChecker;
use YamlStandards\Command\InputSettingData;

class StandardClassesLoaderService
{
    /**
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @return \YamlStandards\Checker\CheckerInterface[]
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
