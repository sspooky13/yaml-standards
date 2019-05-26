<?php

namespace YamlStandards\Command\Service;

use JakubOnderka\PhpParallelLint\RecursiveDirectoryFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use YamlStandards\Command\InputSettingData;

class YamlFilesPathService
{
    /**
     * @param \YamlStandards\Command\InputSettingData $inputSettingData
     * @param bool $includeSkippedFilesToPath
     * @return string[]
     */
    public static function getPathToYamlFiles(
        InputSettingData $inputSettingData,
        $includeSkippedFilesToPath = false
    ) {
        $pathToFiles = [];
        foreach ($inputSettingData->getPathToDirsOrFiles() as $pathToDirOrFile) {
            if (is_file($pathToDirOrFile)) {
                $pathToFiles[] = $pathToDirOrFile;
                continue;
            }

            $recursiveIteratorIterator = self::getRecursiveIteratorIterator($pathToDirOrFile, $inputSettingData->getExcludedPaths(), $includeSkippedFilesToPath);
            $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.(ya?ml(\.dist)?)$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($regexIterator as $pathToFile) {
                $pathToFiles[] = reset($pathToFile);
            }
        }

        $pathToFiles = str_replace('\\', '/', $pathToFiles);

        return array_unique($pathToFiles);
    }

    /**
     * @param string $pathToDir
     * @param string[] $excludedPaths
     * @param bool $includeSkippedFilesToPath
     * @return \RecursiveIteratorIterator
     */
    private static function getRecursiveIteratorIterator(
        $pathToDir,
        array $excludedPaths,
        $includeSkippedFilesToPath = false
    ) {
        $recursiveDirectoryIterator = new RecursiveDirectoryIterator($pathToDir);

        if ($includeSkippedFilesToPath) {
            $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);
        } else {
            $recursiveDirectoryFilterIterator = new RecursiveDirectoryFilterIterator($recursiveDirectoryIterator, $excludedPaths);
            $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryFilterIterator);
        }

        return $recursiveIteratorIterator;
    }
}
