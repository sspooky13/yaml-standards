<?php

namespace YamlStandards\Checker;

use YamlStandards\Command\InputSettingData;

interface CheckerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @return \YamlStandards\Result
     */
    public function check($pathToYamlFile, InputSettingData $inputSettingData);
}
