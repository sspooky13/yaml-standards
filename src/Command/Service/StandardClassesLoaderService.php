<?php

namespace YamlStandards\Command\Service;

use YamlStandards\Command\InputSettingData;
use YamlStandards\Model\YamlAlphabetical\YamlAlphabeticalChecker;
use YamlStandards\Model\YamlIndent\YamlIndentChecker;
use YamlStandards\Model\YamlIndent\YamlIndentFixer;
use YamlStandards\Model\YamlInline\YamlInlineChecker;
use YamlStandards\Model\YamlSpacesBetweenGroups\YamlSpacesBetweenGroupsChecker;

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

        // don't run checker if fix is enabled
        if ($inputSettingData->isFixEnabled() === false) {
            if ($inputSettingData->getCountOfIndents() !== null) {
                $checkerClasses[] = new YamlIndentChecker();
            }
        }

        if ($inputSettingData->checkInlineStandard()) {
            $checkerClasses[] = new YamlInlineChecker();
        }

        if ($inputSettingData->getLevelForCheckSpacesBetweenGroups() !== null) {
            $checkerClasses[] = new YamlSpacesBetweenGroupsChecker();
        }

        return $checkerClasses;
    }

    /**
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @return \YamlStandards\Model\FixerInterface[]
     */
    public static function getFixerClassesByInputSettingData(InputSettingData $inputSettingData)
    {
        $checkerClasses = [];

        if ($inputSettingData->isFixEnabled()) {
            if ($inputSettingData->getCountOfIndents() !== null) {
                $checkerClasses[] = new YamlIndentFixer();
            }
        }

        return $checkerClasses;
    }
}
