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
            if (YamlServiceAliasingDataFactory::belongLineToServices($yamlLines, $key) === false) {
                continue;
            }

            $explodedLine = explode(':', $yamlLine);
            [$lineKey] = $explodedLine;
            $trimmedLineKey = trim($lineKey);
            $countOfRowIndents = YamlService::rowIndentsOf($yamlLine);

            $argumentsShouldBeDefined = $standardParametersData->getServiceArgumentType();
            if ($trimmedLineKey === self::ARGUMENTS_KEY && self::isInsideCallsSection($yamlLines, $key, $countOfRowIndents) === false) {
                $classNameWhereArgumentsBelong = YamlService::getServiceClassName($yamlLines, $key, $countOfRowIndents);
                $class = new ReflectionClass($classNameWhereArgumentsBelong);
                if ($class->isInterface()) {
                    continue;
                }
                $constructor = $class->getConstructor();
                if ($constructor === null) {
                    continue;
                }
                $parameters = $constructor->getParameters();
                $parameterPosition = 0;
                $flowCollectionDepth = 0;
                $argumentItemIndents = null;
                while ($key < count($yamlLines)) {
                    $key++;
                    if (!isset($yamlLines[$key])) {
                        break;
                    }
                    $nextYamlLine = $yamlLines[$key];
                    $trimmedNextLine = trim($nextYamlLine);
                    if ($trimmedNextLine === '') {
                        continue;
                    }
                    $countOfNextRowIndents = YamlService::rowIndentsOf($nextYamlLine);

                    // we are inside a multi-line flow collection ({ } / [ ]) - these lines are part of the
                    // value of a single argument, not arguments themselves, so leave them untouched
                    if ($flowCollectionDepth > 0) {
                        $flowCollectionDepth += self::getFlowCollectionDepthChange($nextYamlLine);
                        continue;
                    }

                    // the arguments block ended - we reached a dedent or a sibling key (e.g. `class:`);
                    // a block sequence item may share the indent of the `arguments:` key, so it does not end the block
                    if (self::isEndOfArgumentsBlock($trimmedNextLine, $countOfNextRowIndents, $countOfRowIndents)) {
                        break;
                    }

                    // the first entry sets the indent shared by all direct argument entries
                    if ($argumentItemIndents === null) {
                        $argumentItemIndents = $countOfNextRowIndents;
                    }

                    // more deeply indented than an entry - this is a nested (block) value of the previous
                    // argument (e.g. a map or list), not an argument itself, so leave it untouched
                    if ($countOfNextRowIndents > $argumentItemIndents) {
                        $flowCollectionDepth += self::getFlowCollectionDepthChange($nextYamlLine);
                        continue;
                    }

                    if ($argumentsShouldBeDefined === self::ARGUMENT_DEFINITION_SPECIFICALLY) {
                        if ($countOfNextRowIndents >= $countOfRowIndents && YamlService::isLineStartOfArrayWithKeyAndValue($trimmedNextLine)) {
                            $explodedNextLine = explode('-', $nextYamlLine, 2);
                            [, $nextLineValue] = $explodedNextLine;
                            $trimmedNextLineValue = trim($nextLineValue);
                            $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfNextRowIndents);
                            $yamlLines[$key] = sprintf('%s$%s: %s', $indents, $parameters[$parameterPosition]->getName(), $trimmedNextLineValue);
                            $parameterPosition++;
                        }
                    }
                    if ($argumentsShouldBeDefined === self::ARGUMENT_DEFINITION_GRADUALLY) {
                        // only touch entries actually written in the specifically form ($param: value); a line already starting with `-` is a dash item (e.g. an inline `- { ... }` value) and must be left untouched
                        if ($countOfNextRowIndents > $countOfRowIndents && strncmp($trimmedNextLine, '-', 1) !== 0 && YamlService::isLineOfParameterDeterminedSpecifically($trimmedNextLine) && trim(explode(':', $trimmedNextLine)[0]) !== self::ARGUMENTS_KEY) {
                            $explodedNextLine = explode(':', $nextYamlLine, 2);
                            if (count($explodedNextLine) >= 2) {
                                [, $nextLineValue] = $explodedNextLine;
                                $trimmedNextLineValue = trim($nextLineValue);
                                $indents = YamlService::createCorrectIndentsByCountOfIndents($countOfNextRowIndents);
                                $yamlLines[$key] = sprintf('%s- %s', $indents, $trimmedNextLineValue);
                            }
                        }
                    }

                    $flowCollectionDepth += self::getFlowCollectionDepthChange($nextYamlLine);
                }
            }
        }

        return $yamlLines;
    }

    /**
     * A block sequence item may share the indent of the `arguments:` key (dash-style list), so it still belongs to the block. Anything else at the same or lower indent is a sibling key or a dedent.
     *
     * @param string $trimmedLine
     * @param int $lineIndents
     * @param int $argumentsIndents
     * @return bool
     */
    private static function isEndOfArgumentsBlock(string $trimmedLine, int $lineIndents, int $argumentsIndents): bool
    {
        if ($lineIndents < $argumentsIndents) {
            return true;
        }

        return $lineIndents === $argumentsIndents && strncmp($trimmedLine, '-', 1) !== 0;
    }

    /**
     * Naive balance of flow-collection brackets on a line: opening ({ [) minus closing (} ]). Used to detect multi-line inline collections so the fixer does not descend into the value of a single argument.
     *
     * @param string $line
     * @return int
     */
    private static function getFlowCollectionDepthChange(string $line): int
    {
        return substr_count($line, '{') + substr_count($line, '[')
            - substr_count($line, '}') - substr_count($line, ']');
    }

    /**
     * @param string[] $yamlLines
     */
    private static function isInsideCallsSection(array $yamlLines, int $currentKey, int $currentIndents): bool
    {
        for ($i = $currentKey - 1; $i >= 0; $i--) {
            $line = $yamlLines[$i];
            if (trim($line) === '') {
                continue;
            }
            $lineIndents = YamlService::rowIndentsOf($line);
            if ($lineIndents < $currentIndents) {
                $trimmedLine = trim($line);
                if (strncmp($trimmedLine, '-', 1) === 0) {
                    $currentIndents = $lineIndents;
                    continue;
                }
                $lineKey = trim(explode(':', $trimmedLine)[0]);

                return $lineKey === 'calls';
            }
        }

        return false;
    }
}
