<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

use YamlStandards\Command\InputSettingData;
use YamlStandards\Model\YamlAlphabetical\YamlAlphabeticalChecker;
use YamlStandards\Model\YamlIndent\YamlIndentChecker;
use YamlStandards\Model\YamlIndent\YamlIndentFixer;
use YamlStandards\Model\YamlInline\YamlInlineChecker;
use YamlStandards\Model\YamlSpacesBetweenGroups\YamlSpacesBetweenGroupsChecker;
use YamlStandards\Model\YamlSpacesBetweenGroups\YamlSpacesBetweenGroupsFixer;

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
            if ($inputSettingData->getLevelForCheckSpacesBetweenGroups() !== null) {
                $checkerClasses[] = new YamlSpacesBetweenGroupsChecker();
            }
        }

        if ($inputSettingData->checkInlineStandard()) {
            $checkerClasses[] = new YamlInlineChecker();
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
            if ($inputSettingData->getLevelForCheckSpacesBetweenGroups() !== null) {
                $checkerClasses[] = new YamlSpacesBetweenGroupsFixer();
            }
        }

        return $checkerClasses;
    }
}
