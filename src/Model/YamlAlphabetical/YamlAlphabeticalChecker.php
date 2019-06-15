<?php

namespace YamlStandards\Model\YamlAlphabetical;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\CheckerInterface;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Result\Result;

/**
 * Check yaml file is alphabetical sorted
 */
class YamlAlphabeticalChecker implements CheckerInterface
{
    /**
     * @inheritDoc
     */
    public function check($pathToYamlFile, InputSettingData $inputSettingData)
    {
        $yamlArrayData = YamlService::getYamlData($pathToYamlFile);
        $yamlArrayDataSorted = $this->sortArray($yamlArrayData, $inputSettingData->getAlphabeticalSortDepth());

        $yamlStringData = Yaml::dump($yamlArrayData, PHP_INT_MAX);
        $yamlStringDataSorted = Yaml::dump($yamlArrayDataSorted, PHP_INT_MAX);

        if ($yamlStringData === $yamlStringDataSorted) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlStringData, $yamlStringDataSorted);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
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
     *
     * @SuppressWarnings("UnusedPrivateMethod") Method is used but PHPMD report he is not
     */
    private function hasArrayKeyUnderscoreAsFirstCharacter($key)
    {
        return strpos($key, '_') === 0;
    }

    /**
     * @param string $key
     * @return bool
     *
     * @SuppressWarnings("UnusedPrivateMethod") Method is used but PHPMD report he is not
     */
    private function hasNotArrayKeyUnderscoreAsFirstCharacter($key)
    {
        return strpos($key, '_') !== 0;
    }
}
