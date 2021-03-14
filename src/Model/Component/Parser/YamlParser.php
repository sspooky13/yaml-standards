<?php

declare(strict_types=1);

namespace YamlStandards\Model\Component\Parser;

use YamlStandards\Model\Component\YamlService;

class YamlParser
{
    /**
     * Go back until deepest parent and count them
     *
     * @param string[] $yamlLines
     * @param int $key
     * @return int
     */
    public static function getCountOfParentsForLine(array $yamlLines, int $key): int
    {
        $countOfParents = 0;
        $line = $yamlLines[$key];
        $originalLine = $line;
        $countOfRowIndents = YamlService::rowIndentsOf($line);
        $trimmedLine = trim($line);
        $isArrayLine = YamlService::hasLineDashOnStartOfLine($trimmedLine);

        while ($key > 0) {
            $currentLine = $yamlLines[$key];
            $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLine);
            $key--;
            $prevLine = $yamlLines[$key];
            $trimmedPrevLine = trim($prevLine);
            $isPrevLineArrayLine = YamlService::hasLineDashOnStartOfLine($trimmedPrevLine);
            $countOfPrevRowIndents = YamlService::rowIndentsOf($prevLine);

            // ignore comment line and empty line
            if (YamlService::isLineBlank($prevLine) || YamlService::isLineComment($prevLine)) {
                continue;
            }

            if (self::isPrevLineNextParent($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents, $isPrevLineArrayLine)) {
                $line = $yamlLines[$key];
                $countOfRowIndents = YamlService::rowIndentsOf($line);
                $trimmedLine = trim($line);
                $isArrayLine = YamlService::hasLineDashOnStartOfLine($trimmedLine);

                /*
                 * array has array values at beginning and is not first element in array, e.g.
                 *  -  pathsToCheck:
                 *      - path/to/file
                 */
                if ($isArrayLine &&
                    $countOfParents > 0 &&
                    YamlService::isLineOpeningAnArray($trimmedLine)
                ) {
                    $countOfParents--;
                }

                $countOfParents++;

                /**
                 * nested hierarchy in array fix, eg.
                 * - foo:
                 *     nested: value
                 *   bar: baz
                 */
                if ($isArrayLine && YamlService::isLineOpeningAnArray($trimmedLine) && YamlService::keyIndentsOf($originalLine) > YamlService::keyIndentsOf($line)) {
                    $countOfParents++;
                }
            }

            // if line has zero counts of indents then it's highest parent and should be ended
            if ($countOfRowIndents === 0) {
                /**
                 * find parent if line belong to array, if it exists then add one parent to count of parents variable, e.g.
                 * foo:
                 * - bar
                 */
                if (YamlService::isLineStartOfArrayWithKeyAndValue($trimmedLine)) {
                    while ($key >= 0) {
                        $prevLine = $yamlLines[$key];
                        $countOfPrevRowIndents = YamlService::rowIndentsOf($prevLine);
                        if (YamlService::isLineBlank($prevLine) || YamlService::isLineComment($prevLine)) {
                            $key--;
                            continue;
                        }

                        /**
                         * 'qux' is highest parent, so skip this, e.g.
                         * - foo:
                         *      bar: baz
                         * - qux:
                         *      - quux: quuz
                         */
                        if ($countOfPrevRowIndents > $countOfCurrentRowIndents) {
                            break;
                        }

                        if ($countOfPrevRowIndents === 0 &&
                            $countOfPrevRowIndents === $countOfCurrentRowIndents &&
                            YamlService::hasLineColon($prevLine) &&
                            YamlService::hasLineValue($prevLine) === false
                        ) {
                            $countOfParents++;

                            break;
                        }

                        $currentLine = $yamlLines[$key];
                        $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLine);
                        $key--;
                    }
                }

                break;
            }
        }

        return $countOfParents;
    }

    /**
     * @param string $pathToYamlFile
     * @return string[]
     */
    public static function getYamlParsedDataFromFile(string $pathToYamlFile): array
    {
        $fileContent = file_get_contents($pathToYamlFile);
        $fileContent = str_replace("\r", '', $fileContent); // remove carriage returns
        $yamlLines = explode("\n", $fileContent);
        $yamlLines = YamlService::mergeMultipleValueLinesTogether($yamlLines);
        $parentsData = [];

        foreach ($yamlLines as $key => $yamlLine) {
            $yamlParserLineData = new YamlParserLineData($yamlLines, $key);
            $yamlLine = YamlParserService::addBlankLineIndentsByHisParent($yamlLines, $key);
            $currentLineWithoutDash = str_replace('-', ' ', $yamlLine);
            $countOfCurrentRowIndents = YamlService::rowIndentsOf($currentLineWithoutDash);
            while ($key > 0) {
                /**
                 * don't associate blank line to previous parent too, e.g.:
                 *   -   foo: bar
                 * (blank line)⬅
                 *   -   baz: qux
                 */
                $nextLine = YamlService::getNextNonCommentAndNonBlankLine($yamlLines, $key);
                if ((YamlService::isLineBlank($yamlLines[$key]) || YamlService::isLineComment($yamlLines[$key])) &&
                    $nextLine !== null && YamlService::isLineStartOfArrayWithKeyAndValue(trim($nextLine)) &&
                    YamlService::rowIndentsOf($nextLine) === 0) {
                    break;
                }

                $key--;
                $prevLine = $yamlLines[$key];
                $prevLineWithoutDash = str_replace('-', ' ', $prevLine);
                $countOfPrevRowIndents = YamlService::rowIndentsOf($prevLineWithoutDash);

                // ignore comment line and empty line
                if (YamlService::isLineBlank($prevLine) || YamlService::isLineComment($prevLine)) {
                    continue;
                }

                if ($countOfPrevRowIndents <= $countOfCurrentRowIndents) {
                    if ($countOfPrevRowIndents < $countOfCurrentRowIndents) {
                        $yamlParserLineData->addLineKey($yamlLines, $key);
                    }
                    $yamlParserLineData->addArrayKeyWithValueToSeparatedArray($yamlLines, $key);

                    $countOfCurrentRowIndents = $countOfPrevRowIndents; // it's parent, so we want to only higher from him
                }

                if ($countOfCurrentRowIndents === 0) {
                    break;
                }
            }

            $parentsData[] = $yamlParserLineData->getPathToLineGradually();
        }

        $allParentsData = array_merge_recursive(...$parentsData); // add all data to one big array

        return $allParentsData;
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @param bool $isPrevLineArrayLine
     * @return bool
     */
    private static function isPrevLineNextParent(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents, bool $isPrevLineArrayLine): bool
    {
        return self::isClassicHierarchy($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents) ||
            self::isStartOfArray($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents, $isPrevLineArrayLine) ||
            self::isStartOfArrayInArray($isArrayLine, $countOfPrevRowIndents, $countOfRowIndents, $isPrevLineArrayLine);
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @return bool
     *
     * @example
     * foo:
     *     bar: baz
     */
    private static function isClassicHierarchy(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents): bool
    {
        return $isArrayLine === false && $countOfPrevRowIndents < $countOfRowIndents;
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @param bool $isPrevLineArrayLine
     * @return bool
     *
     * @example
     * foo:
     *     - bar: baz
     */
    private static function isStartOfArray(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents, bool $isPrevLineArrayLine): bool
    {
        return $isArrayLine && $countOfPrevRowIndents <= $countOfRowIndents && $isPrevLineArrayLine === false;
    }

    /**
     * @param bool $isArrayLine
     * @param int $countOfPrevRowIndents
     * @param int $countOfRowIndents
     * @param bool $isPrevLineArrayLine
     * @return bool
     *
     * @example
     * foo:
     *     - bar:
     *         - 'any text'
     */
    private static function isStartOfArrayInArray(bool $isArrayLine, int $countOfPrevRowIndents, int $countOfRowIndents, bool $isPrevLineArrayLine): bool
    {
        return $isArrayLine && $countOfPrevRowIndents < $countOfRowIndents && $isPrevLineArrayLine;
    }
}
