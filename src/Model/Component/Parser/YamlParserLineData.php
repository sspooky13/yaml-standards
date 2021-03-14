<?php

declare(strict_types=1);

namespace YamlStandards\Model\Component\Parser;

use YamlStandards\Model\Component\YamlService;

class YamlParserLineData
{
    private const KEY = 'key:';
    public const KEY_COMMON_LINE = self::KEY . ':common_line:';
    public const KEY_COMMENT_OR_EMPTY_LINE = self::KEY . 'comment_empty_line:';
    public const KEY_DASH = self::KEY . 'dashes:';
    public const KEY_EMPTY_ARRAY = self::KEY . 'empty_array:';
    public const KEY_ARRAY_WITHOUT_KEY = self::KEY . 'array_without_key:';
    public const KEY_CURLY_BRACKETS = self::KEY . 'curly_brackets:';
    public const KEY_LAST_ELEMENT = 'zzzLastElementInFile:';

    public const EMPTY_LINE_DEFAULT_VALUE = 'empty:line:default:value';

    /**
     * @var string[]
     */
    private $parentKeys;

    /**
     * @var string|string[]
     */
    private $value;

    /**
     * @var int
     */
    private $currentDashRowIndents;

    /**
     * @param string[] $fileLines
     * @param int $key
     */
    public function __construct(array $fileLines, int $key)
    {
        $this->parentKeys = [];

        $isCommentOrBlankLine = false;
        $line = $fileLines[$key];
        $this->currentDashRowIndents = YamlService::rowIndentsOf($line);
        if (YamlService::isLineBlank($line) || YamlService::isLineComment($line)) {
            $this->addCommentOrBlankLineAsKey($fileLines, $key);
            $isCommentOrBlankLine = true;
        } else {
            $this->addLineKey($fileLines, $key);
        }
        $this->addArrayKeyWithValueToSeparatedArray($fileLines, $key);

        $this->value = $this->getLineValue($fileLines, $key, $isCommentOrBlankLine);
    }

    /**
     * @param string[] $fileLines
     * @param int $key
     */
    public function addLineKey(array $fileLines, int $key): void
    {
        if (YamlService::hasLineOnlyOneDash($fileLines[$key])) {
            $countOfRowIndents = YamlService::rowIndentsOf($fileLines[$key]);
            $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfRowIndents);
            $this->parentKeys[] = $correctIndents . self::KEY_DASH . $key;
        } else {
            $this->parentKeys[] = $this->getLineKey($fileLines, $key);
        }
    }

    /**
     * add array elements to separated array, e.g.: - foo: bar
     *
     * @param string[] $fileLines
     * @param int $key
     */
    public function addArrayKeyWithValueToSeparatedArray(array $fileLines, int $key): void
    {
        $currentLine = $fileLines[$key];
        $prevLine = $fileLines[$key - 1] ?? null;
        $currentLineWithoutDash = str_replace('-', ' ', $currentLine);
        $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLineWithoutDash);
        $countOfCurrentRowWithDashIndents = YamlService::rowIndentsOf($currentLine);

        if (YamlService::isLineStartOfArrayWithKeyAndValue(trim($currentLine)) && YamlService::hasLineThreeDashesOnStartOfLine(trim($currentLine)) === false) {
            if ($prevLine !== null) {
                $prevLineWithoutDash = str_replace('-', ' ', $prevLine);
                $countOfPrevRowIndents = YamlService::rowIndentsOf($prevLineWithoutDash);

                $isThereDashInParentKeys = array_filter($this->parentKeys, function ($key) {
                    return strpos($key, self::KEY_EMPTY_ARRAY) !== false;
                });

                if (count($isThereDashInParentKeys) === 0 ||
                    $countOfCurrentRowWithDashIndents < $this->currentDashRowIndents ||
                    ($countOfCurrentRowWithDashIndents === 0 && (YamlService::isLineComment($prevLine) === false || YamlService::isLineNotBlank($prevLine)))
                ) {
                    if ((YamlService::isLineStartOfArrayWithKeyAndValue(trim($currentLine)) && YamlService::isLineStartOfArrayWithKeyAndValue(trim($prevLine)) === false) ||
                        (YamlService::isLineStartOfArrayWithKeyAndValue(trim($currentLine)) && YamlService::isLineStartOfArrayWithKeyAndValue(trim($prevLine)) && $countOfCurrentRowIndents !== $countOfPrevRowIndents)
                        ) {
                        $lineKey = self::KEY_EMPTY_ARRAY . $key;
                        $this->parentKeys[] = $lineKey;
                        $this->currentDashRowIndents = $countOfCurrentRowWithDashIndents;
                    }
                }
            } else {
                $lineKey = self::KEY_EMPTY_ARRAY . $key;
                $this->parentKeys[] = $lineKey;
            }
        }
    }

    /**
     * find next non-blank and non-comment line and associate with him
     *
     * @param string[] $fileLines
     * @param int $key
     */
    public function addCommentOrBlankLineAsKey(array $fileLines, int $key): void
    {
        $currentKey = $key;
        $arrayKeys = array_keys($fileLines);
        $lastKey = end($arrayKeys);
        while ($key < $lastKey) {
            $key++;
            $line = $fileLines[$key];

            /*
             * if next line is single dash then add key as dashes
             * imports:
             *     -
             *         resource: parameters.yml
             * ⬅ you are here
             *     -    ⬅ this is next line
             *         resource: security.yml
            */
            if (YamlService::hasLineOnlyOneDash($fileLines[$key])) {
                $this->parentKeys[] = self::KEY_DASH . $currentKey;
                break;
            }

            if (YamlService::isLineNotBlank($line) && YamlService::isLineComment($line) === false) {
                $lineKey = $this->getLineKey($fileLines, $key);

                $this->parentKeys[] = $lineKey . self::KEY_COMMENT_OR_EMPTY_LINE . $key;
                $this->addArrayKeyWithValueToSeparatedArray($fileLines, $key);

                break;
            }
        }

        // comment/blank line hasn't element to associate
        if ($key === $lastKey) {
            $this->parentKeys[] = self::KEY_LAST_ELEMENT . $currentKey;
        }
    }

    /**
     * Transform path to line value to hierarchy multidimensional path
     *
     * @return string[]
     *
     * @example
     * $parentLineKeys = ['foo', 'bar', 'baz']
     * $line = 'fooBar'
     *
     * to
     *
     * [
     *  'baz' => [
     *      'bar' => [
     *          'foo' => 'fooBar'
     *      ]
     *  ]
     * ]
     */
    public function getPathToLineGradually(): array
    {
        $pathToComment = [];
        $previousKey = null;

        foreach ($this->parentKeys as $lineKey) {
            if ($previousKey === null) {
                $pathToComment = [
                    $lineKey => $this->value,
                ];
            } else {
                $pathToComment[$lineKey] = [
                    $previousKey => $pathToComment[$previousKey],
                ];
                unset($pathToComment[$previousKey]);
            }
            $previousKey = $lineKey;
        }

        return $pathToComment;
    }

    /**
     * @param string[] $fileLines
     * @param int $key
     * @return string
     */
    private function getLineKey(array $fileLines, int $key): string
    {
        $currentLine = $fileLines[$key];
        $currentLineWithoutDash = str_replace('-', ' ', $currentLine);
        $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLineWithoutDash);
        if (YamlService::hasLineDashOnStartOfLine(trim($currentLine)) &&
            YamlService::hasLineThreeDashesOnStartOfLine(trim($currentLine)) === false &&
            YamlService::hasLineOnlyOneDash($currentLine)
        ) {
            [$indentsBeforeDash, $currentLine] = explode('-', $currentLine);
        }

        /**
         * if line start and end with curly bracket return whole line, e.g.: { someKey: someValue }
         */
        if (YamlService::isCurlyBracketInStartOfString(trim($currentLineWithoutDash)) && YamlService::isCurlyBracketInEndOfString(trim($currentLineWithoutDash))) {
            return self::KEY_CURLY_BRACKETS . $key;
        }

        [$lineKey] = explode(':', $currentLine);

        /*
         * foo: ⬅
         *    bar: baz
         */
        if (array_key_exists($key + 1, $fileLines)) {
            $nextLine = $fileLines[$key + 1];
            $nextLineWithoutDash = str_replace('-', ' ', $nextLine);
            $countOfNextRowIndents = YamlService::rowIndentsOf($nextLineWithoutDash);

            if ($countOfCurrentRowIndents < $countOfNextRowIndents) {
                return $currentLine . self::KEY_COMMON_LINE . $key;
            }
        }

        // don't add colon for line without colon, e.g.: "- [seed, ['%faker.seed%']]"
        $colon = YamlService::hasLineColon($currentLine) ? ':' : '';
        // if line is array without key (e.g. "- 'something'") then line can be duplicated, so we need to distinguish them
        $lineNumber = YamlService::hasLineDashOnStartOfLine(trim($currentLine)) && YamlService::hasLineColon($currentLine) === false ? self::KEY_ARRAY_WITHOUT_KEY . $key : '';

        return $lineNumber . $lineKey . $colon . self::KEY_COMMON_LINE . $key;
    }

    /**
     * @param string[] $fileLines
     * @param int $key
     * @param bool $isCommentOrBlankLine
     * @return string|array
     */
    private function getLineValue(array $fileLines, int $key, bool $isCommentOrBlankLine)
    {
        if ($isCommentOrBlankLine) {
            if (YamlService::isLineBlank($fileLines[$key])) {
                return self::EMPTY_LINE_DEFAULT_VALUE;
            }

            return $fileLines[$key];
        }

        $currentLine = $fileLines[$key];
        $currentLineWithoutDash = str_replace('-', ' ', $currentLine);
        $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLineWithoutDash);
        $explodedCurrentLine = explode(':', $currentLine, 2);

        /**
         * if line start and end with curly bracket return empty string, e.g.: { someKey: someValue }
         */
        if (YamlService::isCurlyBracketInStartOfString(trim($currentLineWithoutDash)) && YamlService::isCurlyBracketInEndOfString(trim($currentLineWithoutDash))) {
            return $currentLine;
        }

        /*
         * foo: ⬅
         *    bar: baz
         */
        if (array_key_exists($key + 1, $fileLines)) {
            $nextLine = $fileLines[$key + 1];
            $nextLineWithoutDash = str_replace('-', ' ', $nextLine);
            $countOfNextRowIndents = YamlService::rowIndentsOf($nextLineWithoutDash);
            if ($countOfCurrentRowIndents < $countOfNextRowIndents) {
                return [];
            }
        }

        return YamlService::hasLineValue(trim($currentLine)) ? $explodedCurrentLine[1] : [];
    }
}
