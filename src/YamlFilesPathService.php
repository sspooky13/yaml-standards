<?php

namespace YamlAlphabeticalChecker;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class YamlFilesPathService
{
    /**
     * @param string[] $pathToDirsOrFiles
     * @param string[] $excludedFileMasks
     * @return array
     */
    public static function getPathToYamlFiles(array $pathToDirsOrFiles, array $excludedFileMasks = [])
    {
        $pathToFiles = [];
        foreach ($pathToDirsOrFiles as $pathToDirOrFile) {
            if (is_file($pathToDirOrFile)) {
                $pathToFiles[] = $pathToDirOrFile;
                continue;
            }

            $recursiveDirectoryIterator = new RecursiveDirectoryIterator($pathToDirOrFile);
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

        $pathToFiles = str_replace('\\', '/', $pathToFiles);

        return array_unique($pathToFiles);
    }
}
