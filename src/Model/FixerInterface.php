<?php

namespace YamlStandards\Model;

use YamlStandards\Command\InputSettingData;

interface FixerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param string $pathToDumpFixedFile
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @return \YamlStandards\Result\Result
     */
    public function fix($pathToYamlFile, $pathToDumpFixedFile, InputSettingData $inputSettingData);
}
