<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Command\InputSettingData;
use YamlStandards\Result\Result;

interface FixerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param string $pathToDumpFixedFile
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @return \YamlStandards\Result\Result
     */
    public function fix(string $pathToYamlFile, string $pathToDumpFixedFile, InputSettingData $inputSettingData): Result;
}
