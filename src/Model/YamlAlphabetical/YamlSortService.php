<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlAlphabetical;

use YamlStandards\Model\Component\YamlService;

class YamlSortService
{
    /**
     * @param string[] $yamlArrayData
     * @param int $depth
     * @param string[] $prioritizedKeys
     * @return string[]
     */
    public static function sortArray(array $yamlArrayData, int $depth, array $prioritizedKeys): array
    {
        if ($depth > 0) {
            $yamlArrayData = self::sortArrayKeyWithUnderscoresAsFirst($yamlArrayData, $prioritizedKeys);

            foreach ($yamlArrayData as $key => $value) {
                if (is_array($value)) {
                    // ignore "empty_array" key because they not included in file, they are only auxiliary variables
                    if ($depth > 1 || preg_match(YamlAlphabeticalDataFactory::REGEX_KEY_EMPTY_ARRAY_WITH_NUMBER_AT_END, $key) === 1) {
                        $yamlArrayData[$key] = self::recursiveKsort($value, $depth, $prioritizedKeys);
                    }
                }
            }
        }

        return $yamlArrayData;
    }

    /**
     * @param string[] $yamlArrayData
     * @param int $depth
     * @param string[] $prioritizedKeys
     * @param int $currentDepth
     * @return string[]
     */
    private static function recursiveKsort(array $yamlArrayData, int $depth, array $prioritizedKeys, int $currentDepth = 2): array
    {
        $yamlArrayData = self::sortArrayKeyWithUnderscoresAsFirst($yamlArrayData, $prioritizedKeys);
        foreach ($yamlArrayData as $key => $value) {
            if (is_array($value)) {
                // ignore "empty_array" key because they not included in file, they are only auxiliary variables
                if ($currentDepth < $depth || preg_match(YamlAlphabeticalDataFactory::REGEX_KEY_EMPTY_ARRAY_WITH_NUMBER_AT_END, $key) === 1) {
                    $yamlArrayData[$key] = self::recursiveKsort($value, $depth, $prioritizedKeys, $currentDepth + 1);
                }
            }
        }

        return $yamlArrayData;
    }

    /**
     * @param string[] $yamlArrayData
     * @param string[] $prioritizedKeys
     * @return string[]|string[][]
     */
    private static function sortArrayKeyWithUnderscoresAsFirst(array $yamlArrayData, array $prioritizedKeys): array
    {
        $arrayWithUnderscoreKeys = array_filter($yamlArrayData, [YamlService::class, 'hasArrayKeyUnderscoreAsFirstCharacter'], ARRAY_FILTER_USE_KEY);
        $arrayWithOtherKeys = array_filter($yamlArrayData, [YamlService::class, 'hasNotArrayKeyUnderscoreAsFirstCharacter'], ARRAY_FILTER_USE_KEY);

        uksort($arrayWithUnderscoreKeys, ['self', 'sortArrayAlphabetical']);
        uksort($arrayWithOtherKeys, ['self', 'sortArrayAlphabetical']);

        $arrayData = array_merge($arrayWithUnderscoreKeys, $arrayWithOtherKeys);

        return self::sortArrayElementsByPrioritizedKeys($prioritizedKeys, $arrayData);
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

    /**
     * @param string[] $prioritizedKeys
     * @param string[] $arrayData
     * @return string[]
     */
    private static function sortArrayElementsByPrioritizedKeys(array $prioritizedKeys, array $arrayData): array
    {
        $positionTo = 0;
        foreach ($prioritizedKeys as $prioritizedKey) {
            $foundKeys = preg_grep('/' . $prioritizedKey . '/', array_keys($arrayData));
            foreach ($foundKeys as $foundKey) {
                $positionFrom = (int)array_search($foundKey, array_keys($arrayData), true);
                self::changeElementPositionInArray($arrayData, $positionFrom, $positionTo);
                $positionTo++;
            }
        }

        return $arrayData;
    }

    /**
     * @param string[] $array
     * @param int $positionFrom
     * @param int $positionTo
     *
     * @link https://stackoverflow.com/a/28831998
     */
    private static function changeElementPositionInArray(array &$array, int $positionFrom, int $positionTo): void
    {
        $p1 = array_splice($array, $positionFrom, 1);
        $p2 = array_splice($array, 0, $positionTo);
        $array = array_merge($p2, $p1, $array);
    }
}
