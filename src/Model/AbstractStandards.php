<?php

declare(strict_types=1);

namespace YamlStandards\Model;

use YamlStandards\Model\Component\YamlService;

abstract class AbstractStandards
{
    /**
     * Simply check yaml file is valid, if not \Symfony\Component\Yaml\Yaml class throws \Symfony\Component\Yaml\Exception\ParseException
     *
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    protected function runCheckYamlFileIsValid(string $pathToYamlFile): void
    {
        YamlService::getYamlData($pathToYamlFile);
    }
}
