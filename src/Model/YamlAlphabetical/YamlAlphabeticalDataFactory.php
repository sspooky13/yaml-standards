<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use YamlStandards\Model\Component\Parser\YamlParser;
use YamlStandards\Model\Component\Parser\YamlParserLineData;
use YamlStandards\Model\Component\YamlService;

class YamlAlphabeticalDataFactory
{
    public const REGEX_KEY_COMMON_LINE_WITH_NUMBER_AT_END = '/' . YamlParserLineData::KEY_COMMON_LINE . '\d+$/';
    public const REGEX_KEY_COMMENT_OR_EMPTY_LINE_WITH_NUMBER_AT_END = '/' . YamlParserLineData::KEY_COMMENT_OR_EMPTY_LINE . '\d+$/';
    public const REGEX_KEY_DASH_WITH_NUMBER_AT_END = '/' . YamlParserLineData::KEY_DASH . '\d+$/';
    public const REGEX_KEY_EMPTY_ARRAY_WITH_NUMBER_AT_END = '/' . YamlParserLineData::KEY_EMPTY_ARRAY . '\d+$/';
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
        $yamlArrayDataSorted = YamlSortService::sortArray($yamlArrayData, $depth);

        return self::createRightSortedYamlLines($yamlArrayDataSorted);
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
            if (preg_match(self::REGEX_KEY_COMMENT_OR_EMPTY_LINE_WITH_NUMBER_AT_END, $yamlKey) === 0 &&
                preg_match(self::REGEX_KEY_EMPTY_ARRAY_WITH_NUMBER_AT_END, $yamlKey) === 0
            ) {
                $key = preg_match(self::REGEX_KEY_COMMON_LINE_WITH_NUMBER_AT_END, $yamlKey) === 0 ? $yamlKey : preg_replace(self::REGEX_KEY_COMMON_LINE_WITH_NUMBER_AT_END, '', $yamlKey);
                $key = preg_match(self::REGEX_KEY_DASH_WITH_NUMBER_AT_END, $key) === 0 ? $key : preg_replace(self::REGEX_KEY_DASH_WITH_NUMBER_AT_END, '-', $key);
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
