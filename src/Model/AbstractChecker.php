<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

abstract class AbstractChecker extends AbstractStandards implements CheckerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @return \YamlStandards\Result\Result
     */
    public function runCheck(string $pathToYamlFile, StandardParametersData $standardParametersData): Result
    {
        $this->runCheckYamlFileIsValid($pathToYamlFile);

        return $this->check($pathToYamlFile, $standardParametersData);
    }
}
