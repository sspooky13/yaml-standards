<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use YamlStandards\Model\Component\Parser\YamlParser;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlIndentDataFactory
{
    /**
     * @param string[] $fileLines
     * @param int $key
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @param string $fileLine current checked line in loop
     * @param bool $isCommentLine
     * @return string
     */
    public function getRightFileLines(array $fileLines, int $key, StandardParametersData $standardParametersData, string $fileLine, bool $isCommentLine = false): string
    {
        // return comment line with original indent
        if (YamlService::isLineComment($fileLine) && $standardParametersData->isIgnoreCommentsIndent()) {
            return $fileLine;
        }

        $countOfIndents = $standardParametersData->getIndents();
        $lastFileRow = end($fileLines);

        // add empty line at the end because file can ending with comment line
        if (YamlService::isLineNotBlank($lastFileRow)) {
            $fileLines[] = '';
        }

        if (YamlService::isLineComment($fileLines[$key])) {
            $key++;
            return $this->getRightFileLines($fileLines, $key, $standardParametersData, $fileLine, true);
        }

        $line = $fileLines[$key];
        $trimmedLine = trim($line);
        $countOfRowIndents = YamlService::rowIndentsOf($line);
        $explodedLine = explode(':', $line);
        $fileRows = array_keys($fileLines);
        $lastFileRowKey = end($fileRows);

        // return comment line with original indent
        if ($isCommentLine &&
            $lastFileRowKey === $key &&
            $standardParametersData->getIndentsCommentsWithoutParent() === YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS_COMMENTS_WITHOUT_PARENT_VALUE_PRESERVED
        ) {
            return $fileLine;
        }

        // empty line
        if (YamlService::isLineBlank($line)) {
            /* set comment line indents by next non-empty line, e.g
                (empty line)
                # comment line
                (empty line)
                foo: bar
            */
            if ($isCommentLine && $lastFileRowKey !== $key) {
                $key++;
                return $this->getRightFileLines($fileLines, $key, $standardParametersData, $fileLine, true);
            }

            $correctIndents = YamlService::createCorrectIndentsByCountOfIndents(0);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // the highest parent
        if ($countOfRowIndents === 0) {
            return $this->processHighestParentScenario($fileLines, $key, $fileLine, $isCommentLine, $trimmedLine, $countOfRowIndents, $line, $countOfIndents);
        }

        // line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }" or "- foo:"
        if (YamlService::isLineStartOfArrayWithKeyAndValue($trimmedLine)) {
            return $this->getCorrectLineForArrayWithKeyAndValue($line, $fileLines, $key, $countOfIndents, $fileLine, $isCommentLine);
        }

        // children of array, description over name of function
        if ($this->belongLineToArray($fileLines, $key)) {
            $countOfParents = YamlParser::getCountOfParentsForLine($fileLines, $key);
            $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // line without ':', e.g. array or string
        if (array_key_exists(1, $explodedLine) === false) {
            // is multidimensional array?
            if ($trimmedLine === '-') {
                $countOfParents = YamlParser::getCountOfParentsForLine($fileLines, $key);

                $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
                $trimmedFileLine = trim($fileLine);

                return $correctIndents . $trimmedFileLine;
            }

            // is array or string?
            $countOfParents = YamlParser::getCountOfParentsForLine($fileLines, $key);
            $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        $lineValue = $explodedLine[1];
        $trimmedLineValue = trim($lineValue);

        // parent, not comment line
        if ($isCommentLine === false && (YamlService::isLineBlank($lineValue) || YamlService::isValueReuseVariable($trimmedLineValue))) {
            // fix situation when key is without value and is not parent, e.g.: "   foo:"
            $nextLine = array_key_exists($key + 1, $fileLines) ? $fileLines[$key + 1] : '';
            if (YamlService::rowIndentsOf($nextLine) > $countOfRowIndents) {
                $countOfParents = YamlParser::getCountOfParentsForLine($fileLines, $key);

                $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
                $trimmedFileLine = trim($fileLine);

                return $correctIndents . $trimmedFileLine;
            }
        }

        $countOfParents = YamlParser::getCountOfParentsForLine($fileLines, $key);
        $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
        $trimmedFileLine = trim($fileLine);

        return $correctIndents . $trimmedFileLine;
    }

    /**
     * @param array $fileLines
     * @param int $key
     * @param string $fileLine
     * @param bool $isCommentLine
     * @param string $trimmedLine
     * @param int $countOfRowIndents
     * @param string $line
     * @param int $countOfIndents
     * @return string
     */
    private function processHighestParentScenario(
        array $fileLines,
        int $key,
        string $fileLine,
        bool $isCommentLine,
        string $trimmedLine,
        int $countOfRowIndents,
        string $line,
        int $countOfIndents
    ): string {
        // line is directive
        if (YamlService::hasLineThreeDashesOnStartOfLine($trimmedLine)) {
            $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfRowIndents);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // parent start as array, e.g. "- foo: bar"
        // skip comment line because we want result after this condition
        if ($isCommentLine === false && YamlService::isLineStartOfArrayWithKeyAndValue($trimmedLine)) {
            return $this->getCorrectLineForArrayWithKeyAndValue($line, $fileLines, $key, $countOfIndents, $fileLine, $isCommentLine);
        }

        $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfRowIndents);
        $trimmedFileLine = trim($fileLine);

        return $correctIndents . $trimmedFileLine;
    }

    /**
     * Belong line to children of array, e.g.
     * - foo: bar
     *   baz: qux
     *   quux: quuz
     *   etc.: etc.
     *
     * @param string[] $fileLines
     * @param int $key
     * @return bool
     */
    private function belongLineToArray(array $fileLines, int $key): bool
    {
        while ($key >= 0) {
            $line = $fileLines[$key];
            $key--;
            $prevLine = $fileLines[$key];
            $trimmedPrevLine = trim($prevLine);

            if (YamlService::hasLineDashOnStartOfLine($trimmedPrevLine)) {
                $prevLine = preg_replace('/-/', ' ', $prevLine, 1); // replace '-' for space
            }

            if (YamlService::rowIndentsOf($prevLine) === YamlService::rowIndentsOf($line)) {
                if (YamlService::isLineStartOfArrayWithKeyAndValue($trimmedPrevLine)) {
                    return true;
                }
            } else {
                break;
            }
        }

        return false;
    }

    /**
     * line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }"
     *
     * @param string $line
     * @param string[] $fileLines
     * @param int $key
     * @param int $countOfIndents
     * @param string $fileLine current checked line in loop
     * @param bool $isCommentLine
     * @return string
     */
    private function getCorrectLineForArrayWithKeyAndValue(string $line, array $fileLines, int $key, int $countOfIndents, string $fileLine, bool $isCommentLine): string
    {
        $lineWithReplacedDashToSpace = preg_replace('/-/', ' ', $line, 1);
        $trimmedLineWithoutDash = trim($lineWithReplacedDashToSpace);

        $countOfParents = YamlParser::getCountOfParentsForLine($fileLines, $key);
        $correctIndentsOnStartOfLine = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);

        $trimmedFileLine = trim($fileLine);
        if ($isCommentLine) {
            return $correctIndentsOnStartOfLine . $trimmedFileLine;
        }

        // solution "- { foo: bar }"
        if (YamlService::isCurlyBracketInStartOfString($trimmedLineWithoutDash)) {
            $correctIndentsBetweenDashAndBracket = YamlService::createCorrectIndentsByCountOfIndents(1);

            return $correctIndentsOnStartOfLine . '-' . $correctIndentsBetweenDashAndBracket . $trimmedLineWithoutDash;
        }

        // solution "- foo" (single value of an array)
        if (YamlService::isKeyInStartOfString($trimmedLineWithoutDash) === false) {
            $correctIndentsBetweenDashAndKey = YamlService::createCorrectIndentsByCountOfIndents(1);

            return $correctIndentsOnStartOfLine . '-' . $correctIndentsBetweenDashAndKey . $trimmedLineWithoutDash;
        }

        /**
         * solution for one or more values in array
         * "- foo: bar"
         * "  baz: qux"
         */
        $correctIndentsBetweenDashAndKey = YamlService::createCorrectIndentsByCountOfIndents($countOfIndents - 1); // 1 space is dash, dash is as indent

        return $correctIndentsOnStartOfLine . '-' . $correctIndentsBetweenDashAndKey . $trimmedLineWithoutDash;
    }
}
