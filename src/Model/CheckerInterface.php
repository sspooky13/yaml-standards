<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Command\InputSettingData;
use YamlStandards\Result\Result;

interface CheckerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @return \YamlStandards\Result\Result
     */
    public function check(string $pathToYamlFile, InputSettingData $inputSettingData): Result;
}
