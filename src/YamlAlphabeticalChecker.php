<?php

namespace YamlAlphabeticalChecker;

use Symfony\Component\Yaml\Yaml;

class YamlAlphabeticalChecker
{
    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return bool
     */
    public function isDataSorted($pathToYamlFile)
    {
        $yamlArrayData = $this->parseData($pathToYamlFile);
        $yamlArrayDataSorted = $yamlArrayData;
        $this->recursiveKsort($yamlArrayDataSorted);

        return $yamlArrayData === $yamlArrayDataSorted;
    }

    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string[]
     */
    private function parseData($pathToYamlFile)
    {
        return Yaml::parse(file_get_contents($pathToYamlFile));
    }

    /**
     * @param array $array
     */
    private function recursiveKsort(array &$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->recursiveKsort($value);
            }
        }
        ksort($array);
    }
}
