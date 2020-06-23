<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

abstract class AbstractFixer extends AbstractStandards implements FixerInterface
{
    /**
     * @param string $pathToYamlFile
     * @param string $pathToDumpFixedFile
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @return \YamlStandards\Result\Result
     */
    public function runFix(string $pathToYamlFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $this->runCheckYamlFileIsValid($pathToYamlFile);

        return $this->fix($pathToYamlFile, $pathToDumpFixedFile, $standardParametersData);
    }
}
