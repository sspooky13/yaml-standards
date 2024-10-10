<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument;

use ReflectionClass;
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
            [$lineKey] = $explodedLine;
            $trimmedLineKey = trim($lineKey);
            $countOfRowIndents = YamlService::rowIndentsOf($yamlLine);

            $argumentsShouldBeDefined = 'gradually'; // todo
            if ($trimmedLineKey === self::ARGUMENTS_KEY) {
                if ($argumentsShouldBeDefined === 'specifically') {
                    $classNameWhereArgumentsBelong = YamlService::getServiceClassName($yamlLines, $key, $countOfRowIndents);
                    $class = new ReflectionClass($classNameWhereArgumentsBelong);
                    $constructor = $class->getConstructor();
                    $parameters = $constructor->getParameters();
                    $parameterPosition = 0;
                    while ($key < count($yamlLines)) {
                        $key++;
                        $nextYamlLine = $yamlLines[$key];
                        $countOfNextRowIndents = YamlService::rowIndentsOf($nextYamlLine);
                        $trimmedNextLine = trim($nextYamlLine);
                        if ($countOfNextRowIndents >= $countOfRowIndents && YamlService::isLineStartOfArrayWithKeyAndValue($trimmedNextLine)) {
                            $explodedNextLine = explode('-', $nextYamlLine);
                            [, $nextLineValue] = $explodedNextLine;
                            $trimmedNextLineValue = trim($nextLineValue);
                            $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfNextRowIndents);
                            $yamlLines[$key] = sprintf('%s$%s: %s', $indents, $parameters[$parameterPosition]->getName(), $trimmedNextLineValue);
                            $parameterPosition++;
                        }
                    }
                }
                if ($argumentsShouldBeDefined === 'gradually') {
                    while ($key < count($yamlLines)) {
                        $key++;
                        $nextYamlLine = $yamlLines[$key];
                        $countOfNextRowIndents = YamlService::rowIndentsOf($nextYamlLine);
                        $trimmedNextLine = trim($nextYamlLine);
                        if ($countOfNextRowIndents >= $countOfRowIndents && YamlService::isLineOfParameterDeterminedSpecifically($trimmedNextLine)) {
                            $explodedNextLine = explode(':', $nextYamlLine);
                            [, $nextLineValue] = $explodedNextLine;
                            $trimmedNextLineValue = trim($nextLineValue);
                            $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfNextRowIndents);
                            $yamlLines[$key] = sprintf('%s- %s', $indents, $trimmedNextLineValue);
                        }
                    }
                }
            }
        }

        return $yamlLines;
    }
}
