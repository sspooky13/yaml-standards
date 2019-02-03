<?php

namespace YamlStandards\Checker;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Result;

/**
 * Check yaml file complies right count of indent
 */
class YamlIndentChecker
{
    /**
     * @param string $pathToYamlFile
     * @param int $countOfIndents
     * @return \YamlStandards\Result
     */
    public function getCorrectIndentsInFile($pathToYamlFile, $countOfIndents)
    {
        $fileContent = file_get_contents($pathToYamlFile);
        $fileLines = explode("\n", $fileContent);
        $fileLines = array_map(['self', 'trimCarriageReturn'], $fileLines);
        $rightFileLines = [];

        foreach ($fileLines as $key => $fileLine) {
            $rightFileLines[] = $this->getRightFileLines($fileLines, $key, $countOfIndents, $fileLine);
        }

        $rightFileContent = implode("\n", $rightFileLines);

        if ($fileContent === $rightFileContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($fileContent, $rightFileContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }

    /**
     * @param string[] $fileLines
     * @param int $key
     * @param int $countOfIndents
     * @param string $fileLine current checked line in loop
     * @param bool $isCommentLine
     * @return string
     *
     * @SuppressWarnings("CyclomaticComplexity")
     * @SuppressWarnings("ExcessiveMethodLength")
     */
    private function getRightFileLines(array $fileLines, $key, $countOfIndents, $fileLine, $isCommentLine = false)
    {
        if ($this->isCommentLine($fileLines[$key])) {
            $key++;
            return $this->getRightFileLines($fileLines, $key, $countOfIndents, $fileLine, true);
        }

        $line = $fileLines[$key];
        $trimmedLine = trim($line);
        $countOfRowIndents = strlen($line) - strlen(ltrim($line));
        $explodedLine = explode(':', $line);

        // empty line
        if ($trimmedLine === '') {
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

            $correctIndents = $this->getCorrectIndents(0);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // the highest parent
        if ($countOfRowIndents === 0) {
            // parent start as array, e.g. "- foo: bar"
            // skip comment line because we want result after this condition
            if ($isCommentLine === false && $this->isLineStartOfArrayWithKeyAndValue($trimmedLine)) {
                $removedDashFromLine = ltrim($trimmedLine, '-');
                $correctIndentsOnStartOfLine = $this->getCorrectIndents($countOfRowIndents);
                $trimmedLineWithoutDash = trim($removedDashFromLine);
                $correctIndentsBetweenDashAndKey = $this->getCorrectIndents($countOfIndents - 1); // 1 space is dash, dash is as indent

                return $correctIndentsOnStartOfLine . '-' . $correctIndentsBetweenDashAndKey . $trimmedLineWithoutDash;
            }

            $correctIndents = $this->getCorrectIndents($countOfRowIndents);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }"
        if ($this->isLineStartOfArrayWithKeyAndValue($trimmedLine)) {
            return $this->getCorrectLineForArrayWithKeyAndValue($line, $fileLines, $key, $countOfIndents, $fileLine, $isCommentLine);
        }

        // children of array, description over name of function
        if ($this->belongLineToArray($fileLines, $key)) {
            $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
            $correctIndents = $this->getCorrectIndents($countOfParents * $countOfIndents);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        // line without ':', e.g. array or string
        if (array_key_exists(1, $explodedLine) === false) {
            // is multidimensional array?
            if ($trimmedLine === '-') {
                $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);

                $correctIndents = $this->getCorrectIndents($countOfParents * $countOfIndents);
                $trimmedFileLine = trim($fileLine);

                return $correctIndents . $trimmedFileLine;
            }

            // is array or string?
            $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
            $correctIndents = $this->getCorrectIndents($countOfParents * $countOfIndents);
            $trimmedFileLine = trim($fileLine);

            return $correctIndents . $trimmedFileLine;
        }

        $lineValue = $explodedLine[1];
        $trimmedLineValue = trim($lineValue);

        // parent, not comment line
        if ($isCommentLine === false && ($trimmedLineValue === '' || $this->isValueReuseVariable($trimmedLineValue))) {
            $nextLine = $fileLines[$key + 1];
            $countOfNextRowIndents = strlen($nextLine) - strlen(ltrim($nextLine));
            if ($countOfNextRowIndents > $countOfRowIndents) {
                $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);

                $correctIndents = $this->getCorrectIndents($countOfParents * $countOfIndents);
                $trimmedFileLine = trim($fileLine);

                return $correctIndents . $trimmedFileLine;
            }
        }

        $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
        $correctIndents = $this->getCorrectIndents($countOfParents * $countOfIndents);
        $trimmedFileLine = trim($fileLine);

        return $correctIndents . $trimmedFileLine;
    }

    /**
     * @param string $fileLine
     * @return bool
     */
    protected function isCommentLine($fileLine)
    {
        return preg_match('/^\s*#/', $fileLine) === 1;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function trimCarriageReturn($value)
    {
        return trim($value, "\r");
    }

    /**
     * @param int $countOfIndents
     * @return string
     */
    protected function getCorrectIndents($countOfIndents)
    {
        $currentNumberOfIndents = 1;
        $indents = '';

        while ($currentNumberOfIndents <= $countOfIndents) {
            $indents .= ' ';
            $currentNumberOfIndents++;
        }

        return $indents;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isValueReuseVariable($value)
    {
        return strpos($value, '&') === 0;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function hasLineDashOnStartOfLine($value)
    {
        return strpos($value, '-') === 0;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isCurlyBracketInStartOfString($value)
    {
        return strpos($value, '{') === 0;
    }

    /**
     * line start of array, e.g. "- foo: bar" or "- foo" or "- { foo: bar }"
     *
     * @param string $trimmedLine
     * @return bool
     */
    private function isLineStartOfArrayWithKeyAndValue($trimmedLine)
    {
        return $trimmedLine !== '-' && $this->hasLineDashOnStartOfLine($trimmedLine);
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
    private function belongLineToArray(array $fileLines, $key)
    {
        while ($key >= 0) {
            $line = $fileLines[$key];
            $countOfRowIndents = strlen($line) - strlen(ltrim($line));

            $key--;
            $prevLine = $fileLines[$key];
            $trimmedPrevLine = trim($prevLine);

            if ($this->hasLineDashOnStartOfLine($trimmedPrevLine)) {
                $prevLine = preg_replace('/-/', ' ', $prevLine, 1); // replace '-' for space
            }

            $countOfPrevRowIndents = strlen($prevLine) - strlen(ltrim($prevLine));

            if ($countOfPrevRowIndents === $countOfRowIndents) {
                if ($this->isLineStartOfArrayWithKeyAndValue($trimmedPrevLine)) {
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
    private function getCorrectLineForArrayWithKeyAndValue($line, array $fileLines, $key, $countOfIndents, $fileLine, $isCommentLine)
    {
        $lineWithReplacedDashToSpace = preg_replace('/-/', ' ', $line, 1);
        $trimmedLineWithoutDash = trim($lineWithReplacedDashToSpace);

        $countOfParents = $this->getCountOfParentsForLine($fileLines, $key);
        $correctIndentsOnStartOfLine = $this->getCorrectIndents($countOfParents * $countOfIndents);

        $trimmedFileLine = trim($fileLine);
        if ($isCommentLine) {
            return $correctIndentsOnStartOfLine . $trimmedFileLine;
        }

        // solution "- { foo: bar }"
        if ($this->isCurlyBracketInStartOfString($trimmedLineWithoutDash)) {
            $correctIndentsBetweenDashAndBracket = $this->getCorrectIndents(1);

            return $correctIndentsOnStartOfLine . '-' . $correctIndentsBetweenDashAndBracket . $trimmedLineWithoutDash;
        }

        /**
         * solution for more values in array
         * "- foo: bar"
         * "  baz: qux:
         */
        if (array_key_exists($key + 1, $fileLines) && $this->isNextLineKeyAndValueOfArray($lineWithReplacedDashToSpace, $fileLines[$key + 1])) {
            $correctIndentsBetweenDashAndKey = $this->getCorrectIndents($countOfIndents - 1); // 1 space is dash, dash is as indent

            return $correctIndentsOnStartOfLine . '-' . $correctIndentsBetweenDashAndKey . $trimmedLineWithoutDash;
        }

        // solution for one value in array "- foo: bar" or "- foo"
        $correctIndentsBetweenDashAndKey = $this->getCorrectIndents(1);

        return $correctIndentsOnStartOfLine . '-' . $correctIndentsBetweenDashAndKey . $trimmedLineWithoutDash;
    }

    /**
     * @param string $currentLine
     * @param string $nextLine
     * @return bool
     */
    private function isNextLineKeyAndValueOfArray($currentLine, $nextLine)
    {
        $countOfCurrentRowIndents = strlen($currentLine) - strlen(ltrim($currentLine));
        $countOfNextRowIndents = strlen($nextLine) - strlen(ltrim($nextLine));

        return $countOfCurrentRowIndents === $countOfNextRowIndents;
    }

    /**
     * Go back until deepest parent and count them
     *
     * @param string[] $fileLines
     * @param int $key
     * @return int
     */
    private function getCountOfParentsForLine(array $fileLines, $key)
    {
        $countOfParents = 0;
        $line = $fileLines[$key];
        $countOfRowIndents = strlen($line) - strlen(ltrim($line));
        $trimmedLine = trim($line);
        $isArrayLine = $this->hasLineDashOnStartOfLine($trimmedLine);

        while ($countOfRowIndents !== 0) {
            $key--;
            $prevLine = $fileLines[$key];
            $trimmedPrevLine = trim($prevLine);
            $isPrevLineArrayLine = $this->hasLineDashOnStartOfLine($trimmedPrevLine);
            $countOfPrevRowIndents = strlen($prevLine) - strlen(ltrim($prevLine));

            // ignore comment line and empty line
            if ($trimmedPrevLine === '' || $this->isCommentLine($prevLine)) {
                continue;
            }

            if (/* is start of array in array, e.g.
                   foo:
                     - bar:
                       - 'any text'
                */
                ($isArrayLine && $countOfPrevRowIndents < $countOfRowIndents && $isPrevLineArrayLine) ||
                /* is start of array, e.g.
                   foo:
                     - bar: baz
                */
                ($isArrayLine && $countOfPrevRowIndents <= $countOfRowIndents && $isPrevLineArrayLine === false) ||
                /* is classic hierarchy, e.g.
                   foo:
                     bar: baz
                */
                ($isArrayLine === false && $countOfPrevRowIndents < $countOfRowIndents)
            ) {
                $line = $fileLines[$key];
                $countOfRowIndents = strlen($line) - strlen(ltrim($line));
                $trimmedLine = trim($line);
                $isArrayLine = $this->hasLineDashOnStartOfLine($trimmedLine);

                $countOfParents++;
            }
        }

        return $countOfParents;
    }
}
