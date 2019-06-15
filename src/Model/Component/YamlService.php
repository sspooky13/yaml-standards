<?php

namespace YamlStandards\Model\Component;

use Symfony\Component\Yaml\Yaml;

class YamlService
{
    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string[]
     */
    public static function getYamlData($pathToYamlFile)
    {
        return (array)Yaml::parse(file_get_contents($pathToYamlFile), Yaml::PARSE_CUSTOM_TAGS);
    }
}
