<?php

namespace YamlAlphabeticalChecker;

use SebastianBergmann\Diff\Differ;
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
     * @return string
     */
    public function getDifference($pathToYamlFile)
    {
        $yamlArrayData = $this->parseData($pathToYamlFile);
        $yamlArrayDataSorted = $yamlArrayData;
        $this->recursiveKsort($yamlArrayDataSorted);

        $yamlStringData = Yaml::dump($yamlArrayData, 10, 2);
        $yamlStringDataSorted = Yaml::dump($yamlArrayDataSorted, 10, 2);

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
