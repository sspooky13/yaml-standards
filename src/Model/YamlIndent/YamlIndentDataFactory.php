<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlIndent;

use YamlStandards\Model\Component\YamlService;

class YamlIndentDataFactory
{
    /**
     * @param string[] $fileLines
     * @param int $key
     * @param int $countOfIndents
     * @param string $fileLine current checked line in loop
     * @param bool $isCommentLine
     * @return string
     */
    public function getRightFileLines(array $fileLines, int $key, int $countOfIndents, string $fileLine, bool $isCommentLine = false): string
    {
        if (YamlService::isLineComment($fileLines[$key])) {
            $key++;
            return $this->getRightFileLines($fileLines, $key, $countOfIndents, $fileLine, true);
        }

        $line = $fileLines[$key];
        $trimmedLine = trim($line);
        $countOfRowIndents = YamlService::rowIndentsOf($line);
        $explodedLine = explode(':', $line);

        // empty line
        if (YamlService::isLineBlank($line)) {
            $fileRows = array_keys($fileLines);
            $lastFileRow = end($fileRows);
            /* set comment line indents by next non-empty line, e.g
                (empty line)
                # comment line
                (empty line)
                foo: bar
            */
            if ($isCommentLine && $lastFileRow !== $key) {
                $key++;
                return $this->getRightFileLines($fileLines, $key, $countOfIndents, $fileLine, true);
            }

            $correctIndents = YamlService::createCorrectIndentsByCountOfIndents(0);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // the highest parent
        if ($countOfRowIndents === 0) {
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

        // line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }" or "- foo:"
        if (YamlService::isLineStartOfArrayWithKeyAndValue($trimmedLine)) {
            return $this->getCorrectLineForArrayWithKeyAndValue($line, $fileLines, $key, $countOfIndents, $fileLine, $isCommentLine);
        }

        // children of array, description over name of function
        if ($this->belongLineToArray($fileLines, $key)) {
            $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
            $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // line without ':', e.g. array or string
        if (array_key_exists(1, $explodedLine) === false) {
            // is multidimensional array?
            if ($trimmedLine === '-') {
                $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);

                $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
                $trimmedFileLine = trim($fileLine);

                return $correctIndents . $trimmedFileLine;
            }

            // is array or string?
            $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
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
                $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);

                $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
                $trimmedFileLine = trim($fileLine);

                return $correctIndents . $trimmedFileLine;
            }
        }

        $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
        $correctIndents = YamlService::createCorrectIndentsByCountOfIndents($countOfParents * $countOfIndents);
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

        $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
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

    /**
     * Go back until deepest parent and count them
     *
     * @param string[] $fileLines
     * @param int $key
     * @return int
     */
    private function getCountOfParentsForLine(array $fileLines, int $key): int
    {
        $countOfParents = 0;
        $line = $fileLines[$key];
        $originalLine = $line;
        $countOfRowIndents = YamlService::rowIndentsOf($line);
        $trimmedLine = trim($line);
        $isArrayLine = YamlService::hasLineDashOnStartOfLine($trimmedLine);

        while ($key > 0) {
            $currentLine = $fileLines[$key];
            $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLine);
            $key--;
            $prevLine = $fileLines[$key];
            $trimmedPrevLine = trim($prevLine);
            $isPrevLineArrayLine = YamlService::hasLineDashOnStartOfLine($trimmedPrevLine);
            $countOfPrevRowIndents = YamlService::rowIndentsOf($prevLine);

            // ignore comment line and empty line
            if (YamlService::isLineBlank($prevLine) || YamlService::isLineComment($prevLine)) {
                continue;
            }

            if ($this->isPrevLineNextParent($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents, $isPrevLineArrayLine)) {
                $line = $fileLines[$key];
                $countOfRowIndents = YamlService::rowIndentsOf($line);
                $trimmedLine = trim($line);
                $isArrayLine = YamlService::hasLineDashOnStartOfLine($trimmedLine);

                /*
                 * array has array values at beginning and is not first element in array, e.g.
                 *  -  pathsToCheck:
                 *      - path/to/file
                 */
                if ($isArrayLine &&
                    YamlService::isLineOpeningAnArray($trimmedLine) &&
                    YamlService::rowIndentsOf($line) === 0 &&
                    $countOfParents > 0
                ) {
                    $countOfParents--;
                }

                $countOfParents++;

                /* nested hierarchy in array fix, eg.
                   - foo:
                       nested: value
                     bar: baz
                */
                if ($isArrayLine && YamlService::isLineOpeningAnArray($trimmedLine) && YamlService::keyIndentsOf($originalLine) > YamlService::keyIndentsOf($line)) {
                    $countOfParents++;
                }
            }

            // if line has zero counts of indents then it's highest parent and should be ended
            if ($countOfRowIndents === 0) {
                /**
                 * find parent if line belong to array, if it exists then add one parent to count of parents variable, e.g.
                 * foo:
                 * - bar
                 */
                if (YamlService::isLineStartOfArrayWithKeyAndValue($trimmedLine)) {
                    while ($key >= 0) {
                        $prevLine = $fileLines[$key];
                        $countOfPrevRowIndents = YamlService::rowIndentsOf($prevLine);
                        if (YamlService::isLineBlank($prevLine) || YamlService::isLineComment($prevLine)) {
                            $key--;
                            continue;
                        }

                        /**
                         * 'qux' is highest parent, so skip this, e.g.
                         * - foo:
                         *      bar: baz
                         * - qux:
                         *      - quux: quuz
                         */
                        if ($countOfPrevRowIndents > $countOfCurrentRowIndents) {
                            break;
                        }

                        if ($countOfPrevRowIndents === 0 &&
                            $countOfPrevRowIndents === $countOfCurrentRowIndents &&
                            YamlService::hasLineColon($prevLine) &&
                            YamlService::hasLineValue($prevLine) === false
                        ) {
                            $countOfParents++;

                            break;
                        }

                        $currentLine = $fileLines[$key];
                        $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLine);
                        $key--;
                    }
                }

                break;
            }
        }

        return $countOfParents;
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @param bool $isPrevLineArrayLine
     * @return bool
     */
    private function isPrevLineNextParent(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents, bool $isPrevLineArrayLine): bool
    {
        return $this->isClassicHierarchy($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents) ||
            $this->isStartOfArray($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents, $isPrevLineArrayLine) ||
            $this->isStartOfArrayInArray($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents, $isPrevLineArrayLine);
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @return bool
     *
     * @example
     * foo:
     *     bar: baz
     */
    private function isClassicHierarchy(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents): bool
    {
        return $isArrayLine === false && $countOfPrevRowIndents < $countOfRowIndents;
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @param bool $isPrevLineArrayLine
     * @return bool
     *
     * @example
     * foo:
     *     - bar: baz
     */
    private function isStartOfArray(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents, bool $isPrevLineArrayLine): bool
    {
        return $isArrayLine && $countOfPrevRowIndents <= $countOfRowIndents && $isPrevLineArrayLine === false;
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @param bool $isPrevLineArrayLine
     * @return bool
     *
     * @example
     * foo:
     *     - bar:
     *         - 'any text'
     */
    private function isStartOfArrayInArray(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents, bool $isPrevLineArrayLine): bool
    {
        return $isArrayLine && $countOfPrevRowIndents < $countOfRowIndents && $isPrevLineArrayLine;
    }
}
