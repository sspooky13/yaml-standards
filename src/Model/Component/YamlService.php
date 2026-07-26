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
        return strpos(trim($key), '_') === 0;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasNotArrayKeyUnderscoreAsFirstCharacter(string $key): bool
    {
        return strpos(trim($key), '_') !== 0;
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
    public static function isLineBlank(string $yamlLine): bool
    {
        return trim($yamlLine) === '';
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
    public static function isMultipleLinesValue(string $value): bool
    {
        $value = trim($value);

        return strpos($value, '|') === 0 || strpos($value, '>') === 0;
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
     * @param string $value
     * @return bool
     */
    public static function isCurlyBracketInEndOfString(string $value): bool
    {
        return substr($value, -1) === '}';
    }

    /**
     * line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }" or "- '%parameter%'" or "- '@service'"
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

    /**
     * @param string $line
     * @return bool
     */
    public static function hasLineOnlyOneDash(string $line): bool
    {
        return trim($line) === '-';
    }

    /**
     * specifically parameter line in service file, e.g. "$pathToDir: '%arg_1%'"
     *
     * @param string $line
     * @return bool
     *
     * @example
     * services:
     *      Foo\FooBundle\FooFacade:
     *          arguments:
     *              $pathToDir: '%arg_1%'
     */
    public static function isLineOfParameterDeterminedSpecifically(string $line): bool
    {
        return trim($line) !== '$' && self::hasLineColon($line);
    }

    /**
     * @param string[] $yamlLines
     * @return string[]
     *
     * @example
     * foo: | or <
     *     this is really a
     *     single line of text
     *     despite appearances
     *
     * to
     *
     * foo: | or < \n this is really a \n single line of text \n despite appearances\n
     */
    public static function mergeMultipleValueLinesTogether(array $yamlLines): array
    {
        foreach ($yamlLines as $yamlLineNumber => $yamlLine) {
            $explodedCurrentLine = explode(':', $yamlLine);
            $value = array_key_exists(1, $explodedCurrentLine) ? $explodedCurrentLine[1] : '';
            if (self::isMultipleLinesValue($value)) {
                $countOfCurrentLineIndents = self::rowIndentsOf($yamlLine);
                $explodedLine = explode(':', $yamlLine);
                $value = $explodedLine[1];

                $value .= "\n";
                foreach ($yamlLines as $lineNumber => $fileLine) {
                    if ($lineNumber <= $yamlLineNumber) {
                        continue;
                    }
                    $countOfLineIndents = self::rowIndentsOf($fileLine);

                    if ($countOfLineIndents <= $countOfCurrentLineIndents) {
                        break;
                    }

                    $value .= $fileLine . "\n";
                    unset($yamlLines[$lineNumber]);
                }

                $yamlLines[$yamlLineNumber] = $explodedCurrentLine[0] . ':' . rtrim($value);
            }
        }

        $yamlLines = array_values($yamlLines); // reset array keys

        return $yamlLines;
    }

    /**
     * @param string[] $yamlLines
     * @param int $key
     * @return string|null
     */
    public static function getNextNonCommentAndNonBlankLine(array $yamlLines, int $key): ?string
    {
        $arrayKeys = array_keys($yamlLines);
        $lastKey = end($arrayKeys);
        while ($key < $lastKey) {
            $key++;
            $line = $yamlLines[$key];

            if (self::isLineBlank($line) || self::isLineComment($line)) {
                continue;
            }

            return $line;
        }

        return null;
    }

    /**
     * @param array $yamlLines
     * @param int $key
     * @param int $countOfRowIndents it should be in same position as "class:" definition or other service definitions
     * @return string|null
     */
    public static function getServiceClassName(array $yamlLines, int $key, int $countOfRowIndents): ?string
    {
        while ($key < count($yamlLines)) {
            $key++;
            if (!isset($yamlLines[$key])) {
                break;
            }
            $nextYamlLine = $yamlLines[$key];
            $explodedNextLine = explode(':', $nextYamlLine);
            if (count($explodedNextLine) < 2) {
                continue;
            }
            [$lineKey, $lineValue] = $explodedNextLine;
            $trimmedLineKey = trim($lineKey);
            $trimmedLineValue = trim($lineValue);
            $countOfNextRowIndents = self::rowIndentsOf($nextYamlLine);

            if ($countOfRowIndents === $countOfNextRowIndents) {
                if ($trimmedLineKey === 'class') {
                    return self::removeQuotationMarks($trimmedLineValue);
                }
            }

            if ($countOfNextRowIndents < $countOfRowIndents) {
                break;
            }
        }

        while ($key > 0) {
            $key--;
            $prevLine = $yamlLines[$key];
            $explodedPrevLine = explode(':', $prevLine);
            if (count($explodedPrevLine) < 2) {
                continue;
            }
            [$lineKey, $lineValue] = $explodedPrevLine;
            $trimmedLineKey = trim($lineKey);
            $trimmedLineValue = trim($lineValue);
            $countOfPrevRowIndents = self::rowIndentsOf($prevLine);

            if ($countOfRowIndents === $countOfPrevRowIndents) {
                if ($trimmedLineKey === 'class') {
                    return self::removeQuotationMarks($trimmedLineValue);
                }
            }

            if ($countOfRowIndents > $countOfPrevRowIndents && self::isLineNotBlank($prevLine)) {
                // Extract service name from the line (e.g., "YamlStandardsApp\Service\TestService:" -> "YamlStandardsApp\Service\TestService")
                if (strpos($prevLine, ':') !== false) {
                    $serviceName = explode(':', $prevLine)[0];
                    return trim(self::removeQuotationMarks($serviceName));
                }
            }
        }

        return null;
    }

    /**
     * end of a block belonging to a key indented by $blockIndents; a block sequence item may share the key's indent (dash-style list), so it still belongs to the block, anything else at the same or lower indent is a sibling key or a dedent
     *
     * @param string $trimmedLine
     * @param int $lineIndents
     * @param int $blockIndents
     * @return bool
     */
    public static function isEndOfBlock(string $trimmedLine, int $lineIndents, int $blockIndents): bool
    {
        if ($lineIndents < $blockIndents) {
            return true;
        }

        return $lineIndents === $blockIndents && self::hasLineDashOnStartOfLine($trimmedLine) === false;
    }

    /**
     * naive balance of flow-collection brackets on a line: opening ({ [) minus closing (} ]), used to detect multi-line inline collections so a caller does not descend into a single multi-line value
     *
     * @param string $line
     * @return int
     */
    public static function getFlowCollectionDepthChange(string $line): int
    {
        return substr_count($line, '{') + substr_count($line, '[')
            - substr_count($line, '}') - substr_count($line, ']');
    }

    /**
     * Number of direct entries of a block whose key is on line $blockKey (indented by $blockIndents). Lines that belong to a nested value (a deeper block map/sequence or a multi-line flow collection) are not counted.
     *
     * @param string[] $yamlLines
     * @param int $blockKey
     * @param int $blockIndents
     * @return int
     */
    public static function countDirectEntriesOfBlock(array $yamlLines, int $blockKey, int $blockIndents): int
    {
        $count = 0;
        $flowCollectionDepth = 0;
        $entryIndents = null;
        $key = $blockKey;

        while ($key < count($yamlLines)) {
            $key++;
            if (array_key_exists($key, $yamlLines) === false) {
                break;
            }
            $line = $yamlLines[$key];
            $trimmedLine = trim($line);
            if ($trimmedLine === '') {
                continue;
            }
            $lineIndents = self::rowIndentsOf($line);

            if ($flowCollectionDepth > 0) {
                $flowCollectionDepth += self::getFlowCollectionDepthChange($line);
                continue;
            }
            if (self::isEndOfBlock($trimmedLine, $lineIndents, $blockIndents)) {
                break;
            }
            if ($entryIndents === null) {
                $entryIndents = $lineIndents;
            }
            if ($lineIndents > $entryIndents) {
                $flowCollectionDepth += self::getFlowCollectionDepthChange($line);
                continue;
            }

            $count++;
            $flowCollectionDepth += self::getFlowCollectionDepthChange($line);
        }

        return $count;
    }

    private static function removeQuotationMarks(string $value)
    {
        return str_replace(['"', "'"], '', $value);
    }
}
