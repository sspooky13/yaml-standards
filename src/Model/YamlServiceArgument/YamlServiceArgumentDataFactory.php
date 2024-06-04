<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument;

use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;
use YamlStandards\Model\YamlServiceAliasing\YamlServiceAliasingDataFactory;

class YamlServiceArgumentDataFactory
{
    private const ARGUMENTS_KEY = 'arguments';

    /**
     * @param string[] $yamlLines
     * @param string[][] $yamlParsedData
     * @return string[]
     */
    public static function getCorrectYamlLines(array $yamlLines, array $yamlParsedData, StandardParametersData $standardParametersData): array
    {
        foreach ($yamlLines as $key => $yamlLine) {
            if (!YamlServiceAliasingDataFactory::belongLineToServices($yamlLines, $key)) {
                continue;
            }

            $explodedLine = explode(':', $yamlLine);
            [$lineKey, $lineValue] = $explodedLine;
            $trimmedLineKey = trim($lineKey);
            $trimmedLineValue = trim($lineValue);
            $countOfRowIndents = YamlService::rowIndentsOf($yamlLine);

            $argumentsShouldBeDefined = 'gradually'; // todo
            if ($trimmedLineKey === self::ARGUMENTS_KEY) {
                if ($argumentsShouldBeDefined === 'gradually') {
                    $key++;
                    $nextYamlLine = $yamlLines[$key];
                    $countOfNextRowIndents = YamlService::rowIndentsOf($nextYamlLine);
                    if ($countOfNextRowIndents >= $countOfRowIndents && YamlService::isLineStartOfArrayWithKeyAndValue(trim($nextYamlLine))) {
                        continue;
                    }

                    while ($key > 0) {
                        $nextYamlLine = $yamlLines[$key + 1];
                        $countOfNextRowIndents = YamlService::rowIndentsOf($nextYamlLine);
                        if ($countOfRowIndents === $countOfNextRowIndents) {

                        }
                        $key--;
                        $prevLine = $yamlLines[$key];
                        $trimmedPrevLine = trim($prevLine);
                        $isPrevLineArrayLine = YamlService::hasLineDashOnStartOfLine($trimmedPrevLine);
                        $countOfPrevRowIndents = YamlService::rowIndentsOf($prevLine);
                    }
                }
                if ($argumentsShouldBeDefined === 'specifically') {

                }
            }





            $replacedLineValue = str_replace(['@', '\'', '"'], '', $trimmedLineValue);

            if ($type === YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT) {
                $key--;
                $prevYamlLine = $yamlLines[$key];
                $yamlLines[$key] = $prevYamlLine . ' \'@' . $replacedLineValue . '\'';
                unset($yamlLines[$key + 1]); // remove `alias:` line
            } else {
                $countOfRowIndents = YamlService::rowIndentsOf($yamlLine);
                $nextIndents = $standardParametersData->getIndents(); // `alias:` is child so I need add extra indents
                $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfRowIndents + $nextIndents);
                $yamlLines[$key] = $lineKey . ':';
                $yamlLines[$key . 'alias'] = $indents . 'alias: ' . $replacedLineValue;
            }
        }

        ksort($yamlLines, SORT_NATURAL); // add `key . alias` to right position

        return $yamlLines;
    }
}
