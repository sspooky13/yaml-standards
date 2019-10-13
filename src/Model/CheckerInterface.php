<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

interface CheckerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @return \YamlStandards\Result\Result
     */
    public function check(string $pathToYamlFile, StandardParametersData $standardParametersData): Result;
}
