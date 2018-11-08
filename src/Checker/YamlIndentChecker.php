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
     * @var int
     */
    private $countOfParents;

    public function __construct()
    {
        $this->countOfParents = 0;
    }

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
            $trimmedFileLine = trim($fileLine);
            $indents = $this->getCorrectCountOfIndentsForLine($fileLines, $key, $countOfIndents);

            $rightFileLines[] = $indents . $trimmedFileLine;
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
     * @param bool $isCommentLine
     * @return string
     */
    private function getCorrectCountOfIndentsForLine(array $fileLines, $key, $countOfIndents, $isCommentLine = false)
    {
        if ($this->isCommentLine($fileLines[$key])) {
            $key++;
            $this->getCorrectCountOfIndentsForLine($fileLines, $key, $countOfIndents, true);
        }

        $line = $fileLines[$key];
        $trimmedLine = trim($line);
        $countOfRowIndents = strlen($line) - strlen(ltrim($line));
        $explodedLine = explode(':', $line);

        // empty line
        if ($trimmedLine === '') {
            return $this->getCorrectIndents(0);
        }

        // the highest parent
        if ($countOfRowIndents === 0) {
            $this->countOfParents = 1;

            return $this->getCorrectIndents($countOfRowIndents);
        }

        // next block
        if ($countOfRowIndents < $this->countOfParents * $countOfIndents) {
            while ($countOfRowIndents < $this->countOfParents * $countOfIndents) {
                $this->countOfParents--;
            }
        }

        // line without ':', e.g. array or string
        if (array_key_exists(1, $explodedLine) === false) {
            // is multidimensional array?
            if ($trimmedLine === '-') {
                $countOfParents = $this->countOfParents;
                $this->countOfParents++;

                return $this->getCorrectIndents($countOfParents * $countOfIndents);
            }

            // is array or string?
            return $this->getCorrectIndents($this->countOfParents * $countOfIndents);
        }

        $lineValue = $explodedLine[1];
        $trimmedLineValue = trim($lineValue);

        // parent, not comment line
        if ($isCommentLine === false && ($trimmedLineValue === '' || $this->isValueReuseVariable($trimmedLineValue))) {
            $nextLine = $fileLines[$key + 1];
            $countOfNextRowIndents = strlen($nextLine) - strlen(ltrim($nextLine));
            if ($countOfNextRowIndents > $countOfRowIndents) {
                $countOfParents = $this->countOfParents;
                $this->countOfParents++;

                return $this->getCorrectIndents($countOfParents * $countOfIndents);
            }
        }

        return $this->getCorrectIndents($this->countOfParents * $countOfIndents);
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
}
