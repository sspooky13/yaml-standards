<?php

declare(strict_types=1);

namespace YamlStandards\Model\Component\Parser;

use YamlStandards\Model\Component\YamlService;

class YamlParserService
{
    /**
     * @param string[] $fileLines
     * @param int $key
     * @return string
     */
    public static function addBlankLineIndentsByHisParent(array $fileLines, int $key): string
    {
        $currentLine = $fileLines[$key];
        $arrayKeys = array_keys($fileLines);
        $lastKey = end($arrayKeys);
        $indents = '';

        if (YamlService::isLineNotBlank($currentLine)) {
            return $currentLine;
        }

        while ($key < $lastKey) {
            $key++;
            $line = $fileLines[$key];

            if (YamlService::isLineNotBlank($line) && YamlService::isLineComment($line) === false) {
                $countOfRowIndents = YamlService::rowIndentsOf($line);
                $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfRowIndents);
                break;
            }
        }

        return $indents . $currentLine;
    }
}
