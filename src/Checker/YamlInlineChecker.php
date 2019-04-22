<?php

namespace YamlStandards\Checker;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Result;

/**
 * Check yaml file complies inline standards
 */
class YamlInlineChecker implements CheckerInterface
{
    /**
     * @inheritDoc
     */
    public function check($pathToYamlFile, InputSettingData $inputSettingData)
    {
        $yamlArrayData = $this->parseData($pathToYamlFile);
        $yamlStringData = Yaml::dump($yamlArrayData, 3);

        $yamlContent = file_get_contents($pathToYamlFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);
        $lastYamlElement = end($yamlLines);
        $filteredYamlLines = array_filter($yamlLines, ['self', 'removeBlankLine']);
        $filteredYamlLines = array_filter($filteredYamlLines, ['self', 'removeCommentLine']);
        $filteredYamlLines = array_map(['self', 'removeComments'], $filteredYamlLines);
        if (trim($lastYamlElement) === '') {
            $filteredYamlLines[] = '';
        }

        $filteredYamlFile = implode("\n", $filteredYamlLines);

        if ($yamlStringData === $filteredYamlFile) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($filteredYamlFile, $yamlStringData);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }

    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string[]
     */
    private function parseData($pathToYamlFile)
    {
        return (array)Yaml::parse(file_get_contents($pathToYamlFile), Yaml::PARSE_CUSTOM_TAGS);
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
     * @param string $yamlLine
     * @return bool
     *
     * @SuppressWarnings("UnusedPrivateMethod") Method is used but PHPMD report he is not
     */
    private function removeCommentLine($yamlLine)
    {
        return preg_match('/^\s*#/', $yamlLine) === 0;
    }

    /**
     * @param string $yamlLine
     * @return string
     *
     * @SuppressWarnings("UnusedPrivateMethod") Method is used but PHPMD report he is not
     */
    private function removeComments($yamlLine)
    {
        return preg_replace('/\s#.+/', '', $yamlLine);
    }
}
