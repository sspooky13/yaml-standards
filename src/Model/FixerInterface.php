<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

interface FixerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param string $pathToDumpFixedFile
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @return \YamlStandards\Result\Result
     */
    public function fix(string $pathToYamlFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result;
}
