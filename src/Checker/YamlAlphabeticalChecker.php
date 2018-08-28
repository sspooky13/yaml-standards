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
     * @param int $depth
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string|null
     */
    public function getRightSortedData($pathToYamlFile, $depth)
    {
        $yamlArrayData = $this->parseData($pathToYamlFile);
        $yamlArrayDataSorted = $this->sortArray($yamlArrayData, $depth);

        $yamlStringData = Yaml::dump($yamlArrayData, PHP_INT_MAX);
        $yamlStringDataSorted = Yaml::dump($yamlArrayDataSorted, PHP_INT_MAX);

        if ($yamlStringData === $yamlStringDataSorted) {
            return null;
        }

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
        return Yaml::parse(file_get_contents($pathToYamlFile), Yaml::PARSE_CUSTOM_TAGS);
    }

    /**
     * @param string[] $yamlArrayData
     * @param int $depth
     * @return string[]
     */
    private function sortArray(array $yamlArrayData, $depth)
    {
        if ($depth > 0) {
            $yamlArrayData = $this->sortArrayKeyWithUnderscoresAsFirst($yamlArrayData);

            if ($depth > 1) {
                foreach ($yamlArrayData as $key => $value) {
                    if (is_array($value)) {
                        $yamlArrayData[$key] = $this->recursiveKsort($value, $depth);
                    }
                }
            }
        }

        return $yamlArrayData;
    }

    /**
     * @param string[] $yamlArrayData
     * @param int $depth
     * @param int $currentDepth
     * @return string[]
     */
    private function recursiveKsort(array $yamlArrayData, $depth, $currentDepth = 1)
    {
        $yamlArrayData = $this->sortArrayKeyWithUnderscoresAsFirst($yamlArrayData);
        foreach ($yamlArrayData as $key => $value) {
            if (is_array($value)) {
                $currentDepth++;
                if ($currentDepth < $depth) {
                    $yamlArrayData[$key] = $this->recursiveKsort($value, $depth, $currentDepth);
                }
                continue;
            }
        }

        return $yamlArrayData;
    }

    /**
     * @param string[] $yamlArrayData
     * @return string[]
     */
    private function sortArrayKeyWithUnderscoresAsFirst(array $yamlArrayData)
    {
        $arrayWithUnderscoreKeys = array_filter($yamlArrayData, ['self', 'hasArrayKeyUnderscoreAsFirstCharacter'], ARRAY_FILTER_USE_KEY);
        $arrayWithOtherKeys = array_filter($yamlArrayData, ['self', 'hasNotArrayKeyUnderscoreAsFirstCharacter'], ARRAY_FILTER_USE_KEY);

        ksort($arrayWithUnderscoreKeys);
        ksort($arrayWithOtherKeys);

        return array_merge($arrayWithUnderscoreKeys, $arrayWithOtherKeys);
    }

    /**
     * @param string $key
     * @return bool
     */
    private function hasArrayKeyUnderscoreAsFirstCharacter($key)
    {
        return strpos($key, '_') === 0;
    }

    /**
     * @param string $key
     * @return bool
     */
    private function hasNotArrayKeyUnderscoreAsFirstCharacter($key)
    {
        return strpos($key, '_') !== 0;
    }
}
