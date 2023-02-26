<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlInline;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Model\AbstractChecker;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Result\Result;

/**
 * Check yaml file complies inline standards
 */
class YamlInlineChecker extends AbstractChecker
{
    /**
     * @inheritDoc
     */
    public function check(string $pathToFile, StandardParametersData $standardParametersData): Result
    {
        $yamlArrayData = YamlService::getYamlData($pathToFile);
        $yamlStringData = Yaml::dump($yamlArrayData, 3);

        $yamlContent = file_get_contents($pathToFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);
        $lastYamlElement = end($yamlLines);
        $filteredYamlLines = array_filter($yamlLines, [YamlService::class, 'isLineNotBlank']);
        $filteredYamlLines = array_filter($filteredYamlLines, ['self', 'removeCommentLine']);
        $filteredYamlLines = array_map(['self', 'removeComments'], $filteredYamlLines);
        if (YamlService::isLineBlank($lastYamlElement)) {
            $filteredYamlLines[] = '';
        }

        $filteredYamlFile = implode("\n", $filteredYamlLines);

        if ($yamlStringData === $filteredYamlFile) {
            return new Result($pathToFile, Result::RESULT_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($filteredYamlFile, $yamlStringData);

        return new Result($pathToFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
    }

    /**
     * @param string $yamlLine
     * @return bool
     */
    private function removeCommentLine(string $yamlLine): bool
    {
        return preg_match('/^\s*#/', $yamlLine) === 0;
    }

    /**
     * @param string $yamlLine
     * @return string
     */
    private function removeComments(string $yamlLine): string
    {
        return preg_replace('/\s#.+/', '', $yamlLine);
    }
}
