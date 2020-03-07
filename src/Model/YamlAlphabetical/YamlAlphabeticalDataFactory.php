<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use YamlStandards\Model\Component\Parser\YamlParser;
use YamlStandards\Model\Component\Parser\YamlParserLineData;
use YamlStandards\Model\Component\YamlService;

class YamlAlphabeticalDataFactory
{
    public const REGEX_KEY_DEFAULT_WITH_NUMBER_AT_END = '/' . YamlParserLineData::KEY_DEFAULT . '\d+$/';
    public const REGEX_KEY_DASH_WITH_NUMBER_AT_END = '/' . YamlParserLineData::KEY_DASH . '\d+$/';
    public const REGEX_KEY_ARRAY_WITHOUT_KEY_WITH_NUMBER_AT_END = '/^' . YamlParserLineData::KEY_ARRAY_WITHOUT_KEY . '\d+/';
    public const REGEX_KEY_CURLY_BRACKETS_WITH_NUMBER_AT_END = '/' . YamlParserLineData::KEY_CURLY_BRACKETS . '\d+$/';

    public const REGEX_VALUE_EMPTY_LINE = '/' . YamlParserLineData::EMPTY_LINE_DEFAULT_VALUE . '$/';

    /**
     * @var int
     */
    private static $index;

    /**
     * @param string $pathToYamlFile
     * @param int $depth
     * @return string[]
     */
    public static function getCorrectYamlLines(string $pathToYamlFile, int $depth): array
    {
        self::$index = 0; // start from 0 in every file

        $yamlArrayData = YamlParser::getYamlParsedDataFromFile($pathToYamlFile);
        $yamlArrayDataSorted = self::sortArray($yamlArrayData, $depth);

        return self::createRightSortedYamlLines($yamlArrayDataSorted);
    }

    /**
     * @param string[] $yamlArrayData
     * @param int $depth
     * @return string[]
     */
    private static function sortArray(array $yamlArrayData, int $depth): array
    {
        if ($depth > 0) {
            $yamlArrayData = self::sortArrayKeyWithUnderscoresAsFirst($yamlArrayData);

            if ($depth > 1) {
                foreach ($yamlArrayData as $key => $value) {
                    if (is_array($value)) {
                        $yamlArrayData[$key] = self::recursiveKsort($value, $depth);
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
    private static function recursiveKsort(array $yamlArrayData, int $depth, int $currentDepth = 1): array
    {
        $yamlArrayData = self::sortArrayKeyWithUnderscoresAsFirst($yamlArrayData);
        foreach ($yamlArrayData as $key => $value) {
            if (is_array($value)) {
                if ($currentDepth <= $depth) {
                    $yamlArrayData[$key] = self::recursiveKsort($value, $depth, $currentDepth + 1);
                }
            }
        }

        return $yamlArrayData;
    }

    /**
     * @param string[] $yamlArrayData
     * @return string[]|string[][]
     */
    private static function sortArrayKeyWithUnderscoresAsFirst(array $yamlArrayData): array
    {
        $arrayWithUnderscoreKeys = array_filter($yamlArrayData, [YamlService::class, 'hasArrayKeyUnderscoreAsFirstCharacter'], ARRAY_FILTER_USE_KEY);
        $arrayWithOtherKeys = array_filter($yamlArrayData, [YamlService::class, 'hasNotArrayKeyUnderscoreAsFirstCharacter'], ARRAY_FILTER_USE_KEY);

        uksort($arrayWithUnderscoreKeys, ['self', 'sortArrayAlphabetical']);
        uksort($arrayWithOtherKeys, ['self', 'sortArrayAlphabetical']);

        return array_merge($arrayWithUnderscoreKeys, $arrayWithOtherKeys);
    }

    /**
     * @param string $key1
     * @param string $key2
     * @return int
     */
    private static function sortArrayAlphabetical(string $key1, string $key2): int
    {
        // remove added text for empty line and comment line
        $key1WithoutNumberAtEnd = preg_replace(self::REGEX_KEY_DEFAULT_WITH_NUMBER_AT_END, '', $key1);
        $key2WithoutNumberAtEnd = preg_replace(self::REGEX_KEY_DEFAULT_WITH_NUMBER_AT_END, '', $key2);

        /*
         * add exclamation mark (!) to penultimate position in string for fix order for "dot" key scenario, e.g:
         * foo.bar:
         * foo.bar.baz:
         * ":" is in alphabetical higher as ".", https://s2799303.files.wordpress.com/2013/08/ascii-codes-table1.jpg
         */
        $key1WithoutNumberAtEnd = substr_replace($key1WithoutNumberAtEnd, '!', -1, 0);
        $key2WithoutNumberAtEnd = substr_replace($key2WithoutNumberAtEnd, '!', -1, 0);

        if ($key1WithoutNumberAtEnd === $key2WithoutNumberAtEnd) {
            return strnatcmp(trim($key2), trim($key1));
        }

        return strnatcmp(trim($key1WithoutNumberAtEnd), trim($key2WithoutNumberAtEnd));
    }

    /**
     * @param string[]|string[][] $sortedYamlData
     * @return string[]
     */
    private static function createRightSortedYamlLines(array $sortedYamlData): array
    {
        $rightSortedYamlLines = [];

        foreach ($sortedYamlData as $yamlKey => $yamlValue) {
            if (is_array($yamlValue) === false && ($yamlValue === YamlParserLineData::EMPTY_LINE_DEFAULT_VALUE || YamlService::isLineComment($yamlValue))) {
                $rightSortedYamlLines[] = preg_match(self::REGEX_VALUE_EMPTY_LINE, $yamlValue) === 0 ? $yamlValue : preg_replace(self::REGEX_VALUE_EMPTY_LINE, '', $yamlValue);

                continue;
            }

            $yamlKey = is_int($yamlKey) ? (string)$yamlKey : $yamlKey;
            if (preg_match(self::REGEX_KEY_DEFAULT_WITH_NUMBER_AT_END, $yamlKey) === 0) {
                $key = preg_match(self::REGEX_KEY_DASH_WITH_NUMBER_AT_END, $yamlKey) === 0 ? $yamlKey : preg_replace(self::REGEX_KEY_DASH_WITH_NUMBER_AT_END, '-', $yamlKey);
                $key = preg_match(self::REGEX_KEY_CURLY_BRACKETS_WITH_NUMBER_AT_END, $key) === 0 ? $key : preg_replace(self::REGEX_KEY_CURLY_BRACKETS_WITH_NUMBER_AT_END, '', $key);
                $key = preg_match(self::REGEX_KEY_ARRAY_WITHOUT_KEY_WITH_NUMBER_AT_END, $key) === 0 ? $key : preg_replace(self::REGEX_KEY_ARRAY_WITHOUT_KEY_WITH_NUMBER_AT_END, '', $key);
                $value = is_array($yamlValue) ? '' : $yamlValue;
                $rightSortedYamlLines[] = $key . $value;

                self::$index++;
            }

            if (is_array($yamlValue)) {
                $result = self::createRightSortedYamlLines($yamlValue);
                $rightSortedYamlLines = array_merge($rightSortedYamlLines, $result);
            }
        }

        return $rightSortedYamlLines;
    }
}
