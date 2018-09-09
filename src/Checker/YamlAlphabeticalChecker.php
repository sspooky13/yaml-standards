<?php

namespace YamlAlphabeticalChecker\Checker;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;

/**
 * Check yaml file is alphabetical sorted
 */
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
     * @return string
     */
    public function getDifference($pathToYamlFile)
    {
        $yamlArrayData = $this->parseData($pathToYamlFile);
        $yamlArrayDataSorted = $yamlArrayData;
        $this->recursiveKsort($yamlArrayDataSorted);

        $yamlStringData = Yaml::dump($yamlArrayData, PHP_INT_MAX);
        $yamlStringDataSorted = Yaml::dump($yamlArrayDataSorted, PHP_INT_MAX);

        $differ = new Differ();
        return $differ->diff($yamlStringData, $yamlStringDataSorted);
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
