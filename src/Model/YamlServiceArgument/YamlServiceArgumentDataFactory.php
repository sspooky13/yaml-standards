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

    private const ARGUMENT_DEFINITION_GRADUALLY = YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_GRADUALLY;
    private const ARGUMENT_DEFINITION_SPECIFICALLY = YamlStandardConfigDefinition::CONFIG_PARAMETERS_SERVICE_ARGUMENT_TYPE_VALUE_SPECIFICALLY;

    /**
     * @param string[] $yamlLines
     * @return string[]
     */
    public static function getCorrectYamlLines(array $yamlLines, StandardParametersData $standardParametersData): array
    {
        foreach ($yamlLines as $key => $yamlLine) {
            if (!YamlServiceAliasingDataFactory::belongLineToServices($yamlLines, $key)) {
                continue;
            }

            $explodedLine = explode(':', $yamlLine);
            [$lineKey] = $explodedLine;
            $trimmedLineKey = trim($lineKey);
            $countOfRowIndents = YamlService::rowIndentsOf($yamlLine);

            $argumentsShouldBeDefined = $standardParametersData->getServiceArgumentType();
            if ($trimmedLineKey === self::ARGUMENTS_KEY) {
                $classNameWhereArgumentsBelong = YamlService::getServiceClassName($yamlLines, $key, $countOfRowIndents);
                $class = new ReflectionClass($classNameWhereArgumentsBelong);
                $constructor = $class->getConstructor();
                $parameters = $constructor->getParameters();
                $parameterPosition = 0;
                while ($key < count($yamlLines)) {
                    $key++;
                    if (!isset($yamlLines[$key])) {
                        break;
                    }
                    $nextYamlLine = $yamlLines[$key];
                    $countOfNextRowIndents = YamlService::rowIndentsOf($nextYamlLine);
                    $trimmedNextLine = trim($nextYamlLine);
                    if ($argumentsShouldBeDefined === self::ARGUMENT_DEFINITION_SPECIFICALLY) {
                        if ($countOfNextRowIndents >= $countOfRowIndents && YamlService::isLineStartOfArrayWithKeyAndValue($trimmedNextLine)) {
                            $explodedNextLine = explode('-', $nextYamlLine);
                            [, $nextLineValue] = $explodedNextLine;
                            $trimmedNextLineValue = trim($nextLineValue);
                            $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfNextRowIndents);
                            $yamlLines[$key] = sprintf('%s$%s: %s', $indents, $parameters[$parameterPosition]->getName(), $trimmedNextLineValue);
                            $parameterPosition++;
                        }
                    }
                    if ($argumentsShouldBeDefined === self::ARGUMENT_DEFINITION_GRADUALLY) {
                        if ($countOfNextRowIndents > $countOfRowIndents && YamlService::isLineOfParameterDeterminedSpecifically($trimmedNextLine) && trim(explode(':', $trimmedNextLine)[0]) !== self::ARGUMENTS_KEY) {
                            $explodedNextLine = explode(':', $nextYamlLine);
                            if (count($explodedNextLine) >= 2) {
                                [, $nextLineValue] = $explodedNextLine;
                                $trimmedNextLineValue = trim($nextLineValue);
                                $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfNextRowIndents);
                                $yamlLines[$key] = sprintf('%s- %s', $indents, $trimmedNextLineValue);
                            }
                        }
                    }
                }
            }
        }

        return $yamlLines;
    }
}
