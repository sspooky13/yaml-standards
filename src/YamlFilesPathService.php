<?php

namespace YamlAlphabeticalChecker;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class YamlFilesPathService
{
    /**
     * @param string[] $pathToDirs
     * @param string[] $excludedFileMasks
     * @return array
     */
    public static function getPathToYamlFiles(array $pathToDirs, array $excludedFileMasks = [])
    {
        $pathToFiles = [];
        foreach ($pathToDirs as $pathToDir) {
            $recursiveDirectoryIterator = new RecursiveDirectoryIterator($pathToDir);
            $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);
            $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.yml$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($regexIterator as $pathToFile) {
                $pathToFiles[] = reset($pathToFile);
            }
        }

        foreach ($excludedFileMasks as $excludedFileMask) {
            foreach ($pathToFiles as $key => $pathToFile) {
                if (strpos($pathToFile, $excludedFileMask) !== false) {
                    unset($pathToFiles[$key]);
                }
            }
        }

        return $pathToFiles;
    }
}
