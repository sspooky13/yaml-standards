<?php

namespace YamlStandards\Model\YamlSpacesBetweenGroups;

use SebastianBergmann\Diff\Differ;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Model\CheckerInterface;
use YamlStandards\Result;

/**
 * Check yaml file have space between groups
 */
class YamlSpacesBetweenGroupsChecker implements CheckerInterface
{
    /**
     * @inheritDoc
     */
    public function check($pathToYamlFile, InputSettingData $inputSettingData)
    {
        $yamlContent = file_get_contents($pathToYamlFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);
        $lastYamlElement = end($yamlLines);
        $filteredYamlLines = array_values(array_filter($yamlLines, ['self', 'removeBlankLine']));
        $correctYamlContent = $this->getCorrectYamlContentWithSpacesBetweeenGroups($filteredYamlLines, $inputSettingData->getLevelForCheckSpacesBetweenGroups());

        if (trim($lastYamlElement) === '') {
            $correctYamlContent .= "\n";
        }

        if ($yamlContent === $correctYamlContent) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($yamlContent, $correctYamlContent);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }

    /**
     * @param string[] $yamlLines
     * @param int $level
     * @return string
     */
    private function getCorrectYamlContentWithSpacesBetweeenGroups(array $yamlLines, $level)
    {
        $correctYamlLines = [];

        foreach ($yamlLines as $key => $yamlLine) {
            if ($this->isCommentLine($yamlLine)) {
                $correctYamlLines[$key] = $yamlLine;
                continue;
            }

            $countOfRowIndents = strlen($yamlLine) - strlen(ltrim($yamlLine));
            $lineLevel = $this->getLevelOfCurrentLine($key, $yamlLines, $countOfRowIndents);
            $correctYamlLines[$key] = $yamlLine;

            if ($lineLevel <= $level) {
                $correctYamlLines = $this->getCorrectYamlLinesWithSpace($correctYamlLines, $key);
            }
        }

        ksort($correctYamlLines, SORT_NATURAL);

        return implode("\n", $correctYamlLines);
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    protected function isCommentLine($yamlLine)
    {
        return preg_match('/^\s*#/', $yamlLine) === 1;
    }

    /**
     * @param string $yamlLine
     * @return bool
     *
     * @SuppressWarnings("UnusedPrivateMethod") Method is used but PHPMD report he is not
     */
    private function removeBlankLine($yamlLine)
    {
        return trim($yamlLine) !== '';
    }

    /**
     * @param int $key
     * @param string[] $yamlLines
     * @param int $previousCountOfIndents
     * @param int $currentLineLevel
     * @return int
     */
    private function getLevelOfCurrentLine($key, array $yamlLines, $previousCountOfIndents, $currentLineLevel = 1)
    {
        $yamlLine = $yamlLines[$key];
        $countOfRowIndents = strlen($yamlLine) - strlen(ltrim($yamlLine));
        $key--;

        if ($this->isCommentLine($yamlLine)) {
            return $this->getLevelOfCurrentLine($key, $yamlLines, $previousCountOfIndents, $currentLineLevel);
        }

        if ($countOfRowIndents < $previousCountOfIndents) {
            $currentLineLevel++;
            $previousCountOfIndents = $countOfRowIndents;

            if ($countOfRowIndents > 0) {
                return $this->getLevelOfCurrentLine($key, $yamlLines, $previousCountOfIndents, $currentLineLevel);
            }
        }

        if ($countOfRowIndents > 0) {
            return $this->getLevelOfCurrentLine($key, $yamlLines, $previousCountOfIndents, $currentLineLevel);
        }

        return $currentLineLevel;
    }

    /**
     * add empty line before current line if current line is not first element in parent
     *
     * @param string[] $correctYamlLines
     * @param int $key
     * @return string[]
     */
    private function getCorrectYamlLinesWithSpace($correctYamlLines, $key)
    {
        $yamlLine = $correctYamlLines[$key];
        $countOfRowIndents = strlen($yamlLine) - strlen(ltrim($yamlLine));
        $key--;

        if (reset($correctYamlLines) === $yamlLine) {
            return $correctYamlLines;
        }

        while (array_key_exists($key, $correctYamlLines) && $this->isCommentLine($correctYamlLines[$key])) {
            $key--;
        }

        if (array_key_exists($key, $correctYamlLines) === false) {
            return $correctYamlLines;
        }

        $previousYamlLine = $correctYamlLines[$key];
        $previousCountOfRowIndents = strlen($previousYamlLine) - strlen(ltrim($previousYamlLine));

        if ($previousCountOfRowIndents < $countOfRowIndents) {
            return $correctYamlLines;
        }

        $correctYamlLines[$key . 'space'] = '';

        return $correctYamlLines;
    }
}
