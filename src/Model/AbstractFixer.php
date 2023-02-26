<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

abstract class AbstractFixer extends AbstractStandards implements FixerInterface
{
    /**
     * @param string $pathToFile
     * @param string $pathToDumpFixedFile
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @return \YamlStandards\Result\Result
     */
    public function runFix(string $pathToFile, string $pathToDumpFixedFile, StandardParametersData $standardParametersData): Result
    {
        $this->runCheckYamlFileIsValid($pathToFile);

        return $this->fix($pathToFile, $pathToDumpFixedFile, $standardParametersData);
    }
}
