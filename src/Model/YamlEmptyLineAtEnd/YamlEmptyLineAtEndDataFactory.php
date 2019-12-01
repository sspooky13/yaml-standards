<?php

declare(strict_types = 1);

namespace YamlStandards\Model\YamlEmptyLineAtEnd;

use YamlStandards\Model\Component\YamlService;

class YamlEmptyLineAtEndDataFactory
{
    /**
     * @param string[] $yamlLines
     * @return string[]
     */
    public static function getCorrectYamlContent(array $yamlLines): array
    {
        $reversedYamlLines = array_reverse($yamlLines, true);
        $keys = array_keys($reversedYamlLines);
        $lastKey = end($keys);

        foreach ($reversedYamlLines as $key => $yamlLine) {
            if ($key === $lastKey) {
                $yamlLines = [''];
                break;
            }
            if (YamlService::isLineNotBlank($yamlLine)) {
                $yamlLines = array_slice($yamlLines, 0, $key + 1);
                $yamlLines[] = '';
                break;
            }

            continue;
        }

        return $yamlLines;
    }
}
