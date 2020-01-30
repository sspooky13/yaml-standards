<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceAliasing;

use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\StandardParametersData;
use YamlStandards\Model\Config\YamlStandardConfigDefinition;

class YamlServiceAliasingDataFactory
{
    private const SERVICES_KEY = 'services:';
    private const SHORT_TYPE_REGEX = '/^\s+\S+:\s*\'@\S+\'$/';
    private const LONG_TYPE_REGEX = '/^\s+alias:\s*\S+$/';

    /**
     * @param string[] $yamlLines
     * @return bool
     */
    public static function existsServicesInHighestParent(array $yamlLines): bool
    {
        $trimmedYamlLines = array_map(static function ($yamlLine) {
            return rtrim($yamlLine);
        }, $yamlLines);

        return in_array(self::SERVICES_KEY, $trimmedYamlLines, true);
    }

    /**
     * @param string[] $yamlLines
     * @param string[][] $yamlParsedData
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     * @return string[]
     */
    public static function getCorrectYamlLines(array $yamlLines, array $yamlParsedData, StandardParametersData $standardParametersData): array
    {
        foreach ($yamlLines as $key => $yamlLine) {
            $type = $standardParametersData->getServiceAliasingType();
            if (self::isLineOppositeAliasByType($yamlLine, $type) &&
                self::belongLineToServices($yamlLines, $key) &&
                self::isAliasStandalone($yamlLines, $yamlParsedData, $key, $type)
            ) {
                $explodedLine = explode(':', $yamlLine);
                [$lineKey, $lineValue] = $explodedLine;
                $trimmedLineValue = trim($lineValue);
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
        }

        ksort($yamlLines, SORT_NATURAL); // add `key . alias` to right position

        return $yamlLines;
    }

    /**
     * @param string[] $yamlLines
     * @param int $key
     * @return bool
     */
    private static function belongLineToServices(array $yamlLines, int $key): bool
    {
        while ($key > 0) {
            $key--;

            // some lines could removed so I need to check that line exists
            if (array_key_exists($key, $yamlLines) === false) {
                continue;
            }

            $yamlLine = $yamlLines[$key];
            $trimmedYamlLine = rtrim($yamlLine);
            $countOfRowIndents = YamlService::rowIndentsOf($trimmedYamlLine);

            if ($countOfRowIndents === 0 && $trimmedYamlLine === self::SERVICES_KEY) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $yamlLine
     * @param string $type
     * @return bool
     */
    private static function isLineOppositeAliasByType(string $yamlLine, string $type): bool
    {
        if ($type === YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_SHORT) {
            return preg_match(self::LONG_TYPE_REGEX, $yamlLine) === 1; // long because we want find opposite aliases
        }

        return preg_match(self::SHORT_TYPE_REGEX, $yamlLine) === 1; // short because we want find opposite aliases
    }

    /**
     * @param string[] $yamlLines
     * @param string[][] $yamlParsedData
     * @param int $key
     * @param string $type
     * @return bool
     */
    private static function isAliasStandalone(array $yamlLines, array $yamlParsedData, int $key, string $type): bool
    {
        $currentYamlLine = $yamlLines[$key];

        // is located alias as direct child (not argument or something like that)
        if ($type === YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ALIASING_TYPE_VALUE_LONG) {
            $explodedLine = explode(':', $currentYamlLine);
            $lineKey = trim(reset($explodedLine));

            return array_key_exists($lineKey, $yamlParsedData['services']);
        }

        $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentYamlLine);
        $prevYamlLine = $yamlLines[$key - 1];
        $countOfPrevRowIndents = YamlService::rowIndentsOf($prevYamlLine);
        $hasNextRowLessCountOfIndentsAsCurrent = true;

        // alias can be last element in file
        if (array_key_exists($key + 1, $yamlLines)) {
            $nextYamlLine = $yamlLines[$key + 1];
            $countOfNextRowIndents = YamlService::rowIndentsOf($nextYamlLine);

            $hasNextRowLessCountOfIndentsAsCurrent = $countOfNextRowIndents < $countOfCurrentRowIndents;
        }

        if ($countOfPrevRowIndents < $countOfCurrentRowIndents &&
            $hasNextRowLessCountOfIndentsAsCurrent &&
            YamlService::hasLineColon($prevYamlLine) &&
            YamlService::hasLineValue($prevYamlLine) === false &&
            strpos($prevYamlLine, 'arguments:') === false
        ) {
            return true;
        }

        return false;
    }
}
