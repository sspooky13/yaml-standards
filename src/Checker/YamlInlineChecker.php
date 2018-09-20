<?php

namespace YamlAlphabeticalChecker\Checker;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;

/**
 * Check yaml file complies inline standards
 */
class YamlInlineChecker
{
    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string|null
     */
    public function getRightCompilesData($pathToYamlFile)
    {
        $yamlArrayData = $this->parseData($pathToYamlFile);
        $yamlStringData = Yaml::dump($yamlArrayData, 3);

        $yamlContent = file_get_contents($pathToYamlFile);
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
            return null;
        }

        $differ = new Differ();
        return $differ->diff($yamlStringData, $filteredYamlFile);
    }

    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string[]
     */
    private function parseData($pathToYamlFile)
    {
        return Yaml::parse(file_get_contents($pathToYamlFile), Yaml::PARSE_CUSTOM_TAGS);
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    private function removeBlankLine($yamlLine)
    {
        return trim($yamlLine) !== '';
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    private function removeCommentLine($yamlLine)
    {
        return preg_match('/^\s*#/', $yamlLine) === 0;
    }

    /**
     * @param string $yamlLine
     * @return string
     */
    private function removeComments($yamlLine)
    {
        return preg_replace('/\s#.+/', '', $yamlLine);
    }
}
