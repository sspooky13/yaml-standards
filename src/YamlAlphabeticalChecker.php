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
     * @return string
     */
    public function getDifference($pathToYamlFile)
    {
        $yamlArrayData = $this->parseData($pathToYamlFile);
        $yamlArrayDataSorted = $yamlArrayData;
        $this->recursiveKsort($yamlArrayDataSorted);

        $yamlStringData = Yaml::dump($yamlArrayData, 10, 2);
        $yamlStringDataSorted = Yaml::dump($yamlArrayDataSorted, 10, 2);

        $yamlDataExploded = explode("\n", $yamlStringData);
        $yamlSortedDataExploded = explode("\n", $yamlStringDataSorted);
        $yamlDifference = "\n";

        foreach ($yamlDataExploded as $key => $value) {
            $currentSortedLine = $yamlSortedDataExploded[$key];

            if ($value !== $currentSortedLine) {
                $yamlDifference .= '<fg=red>- ' . $value . "\n</fg=red>";
                $yamlDifference .= '<fg=green>+ ' . $currentSortedLine . "\n</fg=green>";
            } else {
                $yamlDifference .= $value . "\n";
            }
        }

        return $yamlDifference;
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
