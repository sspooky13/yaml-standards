<?php

declare(strict_types=1);

namespace YamlStandards\Model\Component;

use Symfony\Component\Yaml\Inline;
use Symfony\Component\Yaml\Yaml;

class YamlService
{
    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string[]|string[][]
     */
    public static function getYamlData(string $pathToYamlFile): array
    {
        return (array)Yaml::parse(file_get_contents($pathToYamlFile), Yaml::PARSE_CUSTOM_TAGS);
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasArrayKeyUnderscoreAsFirstCharacter(string $key): bool
    {
        return strpos($key, '_') === 0;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasNotArrayKeyUnderscoreAsFirstCharacter(string $key): bool
    {
        return strpos($key, '_') !== 0;
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    public static function isLineNotBlank(string $yamlLine): bool
    {
        return trim($yamlLine) !== '';
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    public static function isLineComment(string $yamlLine): bool
    {
        return preg_match('/^\s*#/', $yamlLine) === 1;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isValueReuseVariable(string $value): bool
    {
        return strpos($value, '&') === 0;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function hasLineDashOnStartOfLine(string $value): bool
    {
        return strpos($value, '-') === 0;
    }

    /**
     * @param string $trimmedLine
     * @return bool
     */
    public static function hasLineThreeDashesOnStartOfLine(string $trimmedLine): bool
    {
        return strpos($trimmedLine, '---') === 0;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isCurlyBracketInStartOfString(string $value): bool
    {
        return strpos($value, '{') === 0;
    }

    /**
     * line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }"
     *
     * @param string $trimmedLine
     * @return bool
     */
    public static function isLineStartOfArrayWithKeyAndValue(string $trimmedLine): bool
    {
        return $trimmedLine !== '-' && self::hasLineDashOnStartOfLine($trimmedLine);
    }

    /**
     * value starting with key, e.g. 'foo: bar' or '"foo bar": baz'
     *
     * @param string $value
     * @return bool
     */
    public static function isKeyInStartOfString(string $value): bool
    {
        return (bool)preg_match('~^(' . Inline::REGEX_QUOTED_STRING . '|[^ \'"{\[].*?) *:(\s|$)~u', $value);
    }

    /**
     * line possibly opening an array, e.g. 'foo:' or '- foo:'
     *
     * @param string $trimmedLine
     * @return bool
     */
    public static function isLineOpeningAnArray(string $trimmedLine): bool
    {
        return (bool)preg_match('~^(- +)*(' . Inline::REGEX_QUOTED_STRING . '|[^ \'"{\[].*?) *:$~u', $trimmedLine);
    }

    /**
     * @param string $line
     * @return int
     */
    public static function rowIndentsOf(string $line): int
    {
        return strlen($line) - strlen(ltrim($line));
    }

    /**
     * @param string $line
     * @return int
     */
    public static function keyIndentsOf(string $line): int
    {
        return strlen($line) - strlen(ltrim($line, '- '));
    }

    /**
     * @param int $countOfIndents
     * @return string
     */
    public static function createCorrectIndentsByCountOfIndents(int $countOfIndents): string
    {
        return str_repeat(' ', $countOfIndents);
    }

    /**
     * @param string $line
     * @return bool
     */
    public static function hasLineValue(string $line): bool
    {
        $explodedLine = explode(':', $line);

        return array_key_exists(1, $explodedLine) && self::isLineNotBlank($explodedLine[1]);
    }

    /**
     * @param string $line
     * @return bool
     */
    public static function hasLineColon(string $line): bool
    {
        return strpos($line, ':') !== false;
    }
}
