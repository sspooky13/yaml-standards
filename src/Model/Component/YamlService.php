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
     * @return string[]
     */
    public static function getYamlData($pathToYamlFile)
    {
        return (array)Yaml::parse(file_get_contents($pathToYamlFile), Yaml::PARSE_CUSTOM_TAGS);
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasArrayKeyUnderscoreAsFirstCharacter($key)
    {
        return strpos($key, '_') === 0;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasNotArrayKeyUnderscoreAsFirstCharacter($key)
    {
        return strpos($key, '_') !== 0;
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    public static function isLineNotBlank($yamlLine)
    {
        return trim($yamlLine) !== '';
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    public static function isLineComment($yamlLine)
    {
        return preg_match('/^\s*#/', $yamlLine) === 1;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isValueReuseVariable($value)
    {
        return strpos($value, '&') === 0;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function hasLineDashOnStartOfLine($value)
    {
        return strpos($value, '-') === 0;
    }

    /**
     * @param string $trimmedLine
     * @return bool
     */
    public static function hasLineThreeDashesOnStartOfLine($trimmedLine)
    {
        return strpos($trimmedLine, '---') === 0;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isCurlyBracketInStartOfString($value)
    {
        return strpos($value, '{') === 0;
    }

    /**
     * line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }"
     *
     * @param string $trimmedLine
     * @return bool
     */
    public static function isLineStartOfArrayWithKeyAndValue($trimmedLine)
    {
        return $trimmedLine !== '-' && self::hasLineDashOnStartOfLine($trimmedLine);
    }

    /**
     * value starting with key, e.g. 'foo: bar' or '"foo bar": baz'
     *
     * @param string $value
     * @return bool
     */
    public static function isKeyInStartOfString($value)
    {
        return (bool)preg_match('~^(' . Inline::REGEX_QUOTED_STRING . '|[^ \'"{\[].*?) *:(\s|$)~u', $value);
    }

    /**
     * line possibly opening an array, e.g. 'foo:' or '- foo:'
     *
     * @param string $trimmedLine
     * @return bool
     */
    public static function isLineOpeningAnArray($trimmedLine)
    {
        return (bool)preg_match('~^(- +)*(' . Inline::REGEX_QUOTED_STRING . '|[^ \'"{\[].*?) *:$~u', $trimmedLine);
    }

    /**
     * @param string $line
     * @return int
     */
    public static function rowIndentsOf($line)
    {
        return strlen($line) - strlen(ltrim($line));
    }

    /**
     * @param string $line
     * @return int
     */
    public static function keyIndentsOf($line)
    {
        return strlen($line) - strlen(ltrim($line, '- '));
    }
}
