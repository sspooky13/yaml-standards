<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlInline;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Command\InputSettingData;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Model\CheckerInterface;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Result\Result;

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
        $yamlArrayData = YamlService::getYamlData($pathToYamlFile);
        $yamlStringData = Yaml::dump($yamlArrayData, 3);

        $yamlContent = file_get_contents($pathToYamlFile);
        $yamlContent = str_replace("\r", '', $yamlContent); // remove carriage returns
        $yamlLines = explode("\n", $yamlContent);
        $lastYamlElement = end($yamlLines);
        $filteredYamlLines = array_filter($yamlLines, [YamlService::class, 'isLineNotBlank']);
        $filteredYamlLines = array_filter($filteredYamlLines, ['self', 'removeCommentLine']);
        $filteredYamlLines = array_map(['self', 'removeComments'], $filteredYamlLines);
        if (trim($lastYamlElement) === '') {
            $filteredYamlLines[] = '';
        }

        $filteredYamlFile = implode("\n", $filteredYamlLines);

        if ($yamlStringData === $filteredYamlFile) {
            return new Result($pathToYamlFile, Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        }

        $differ = new Differ();
        $diffBetweenStrings = $differ->diff($filteredYamlFile, $yamlStringData);

        return new Result($pathToYamlFile, Result::RESULT_CODE_INVALID_FILE_SYNTAX, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX, $diffBetweenStrings);
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
