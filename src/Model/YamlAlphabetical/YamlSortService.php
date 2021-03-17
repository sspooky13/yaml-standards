<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use YamlStandards\Model\Component\YamlService;

class YamlSortService
{
    /**
     * @param string[] $yamlArrayData
     * @param int $depth
     * @return string[]
     */
    public static function sortArray(array $yamlArrayData, int $depth): array
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
        $key1WithoutNumberAtEnd = preg_replace(YamlAlphabeticalDataFactory::REGEX_KEY_COMMENT_OR_EMPTY_LINE_WITH_NUMBER_AT_END, '', $key1);
        $key2WithoutNumberAtEnd = preg_replace(YamlAlphabeticalDataFactory::REGEX_KEY_COMMENT_OR_EMPTY_LINE_WITH_NUMBER_AT_END, '', $key2);

        // add key number to end for fix situation when keys are same
        preg_match('/\d+$/', $key1WithoutNumberAtEnd, $key1NumberAtEnd);
        preg_match('/\d+$/', $key2WithoutNumberAtEnd, $key2NumberAtEnd);
        $key1NumberAtEnd = count($key1NumberAtEnd) === 0 ? 0 : reset($key1NumberAtEnd);
        $key2NumberAtEnd = count($key2NumberAtEnd) === 0 ? 0 : reset($key2NumberAtEnd);

        $key1WithoutNumberAtEnd = preg_match(YamlAlphabeticalDataFactory::REGEX_KEY_COMMON_LINE_WITH_NUMBER_AT_END, $key1WithoutNumberAtEnd) === 0 ? $key1WithoutNumberAtEnd : preg_replace(YamlAlphabeticalDataFactory::REGEX_KEY_COMMON_LINE_WITH_NUMBER_AT_END, '', $key1WithoutNumberAtEnd);
        $key1WithoutNumberAtEnd = preg_match(YamlAlphabeticalDataFactory::REGEX_KEY_ARRAY_WITHOUT_KEY_WITH_NUMBER_AT_END, $key1WithoutNumberAtEnd) === 0 ? $key1WithoutNumberAtEnd : preg_replace(YamlAlphabeticalDataFactory::REGEX_KEY_ARRAY_WITHOUT_KEY_WITH_NUMBER_AT_END, '', $key1WithoutNumberAtEnd);

        $key2WithoutNumberAtEnd = preg_match(YamlAlphabeticalDataFactory::REGEX_KEY_COMMON_LINE_WITH_NUMBER_AT_END, $key2WithoutNumberAtEnd) === 0 ? $key2WithoutNumberAtEnd : preg_replace(YamlAlphabeticalDataFactory::REGEX_KEY_COMMON_LINE_WITH_NUMBER_AT_END, '', $key2WithoutNumberAtEnd);
        $key2WithoutNumberAtEnd = preg_match(YamlAlphabeticalDataFactory::REGEX_KEY_ARRAY_WITHOUT_KEY_WITH_NUMBER_AT_END, $key2WithoutNumberAtEnd) === 0 ? $key2WithoutNumberAtEnd : preg_replace(YamlAlphabeticalDataFactory::REGEX_KEY_ARRAY_WITHOUT_KEY_WITH_NUMBER_AT_END, '', $key2WithoutNumberAtEnd);

        /*
         * add exclamation mark (!) to penultimate position in string for fix order for "dot" key scenario, e.g:
         * foo.bar:
         * foo.bar.baz:
         * ":" is in alphabetical higher as ".", https://s2799303.files.wordpress.com/2013/08/ascii-codes-table1.jpg
         */
        $key1WithoutNumberAtEnd = substr_replace($key1WithoutNumberAtEnd, '!', -1, 0);
        $key2WithoutNumberAtEnd = substr_replace($key2WithoutNumberAtEnd, '!', -1, 0);

        $key1WithoutNumberAtEnd .= $key1NumberAtEnd;
        $key2WithoutNumberAtEnd .= $key2NumberAtEnd;

        if ($key1WithoutNumberAtEnd === $key2WithoutNumberAtEnd) {
            return strnatcmp(trim($key2), trim($key1));
        }

        return strnatcmp(trim($key1WithoutNumberAtEnd), trim($key2WithoutNumberAtEnd));
    }
}
