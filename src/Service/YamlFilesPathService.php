<?php

namespace YamlStandards\Service;

use JakubOnderka\PhpParallelLint\RecursiveDirectoryFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class YamlFilesPathService
{
    /**
     * @param string[] $pathToDirsOrFiles
     * @param string[] $excludedPaths
     * @return string[]
     */
    public static function getPathToYamlFiles(array $pathToDirsOrFiles, array $excludedPaths)
    {
        $pathToFiles = [];
        foreach ($pathToDirsOrFiles as $pathToDirOrFile) {
            if (is_file($pathToDirOrFile)) {
                $pathToFiles[] = $pathToDirOrFile;
                continue;
            }

            $recursiveDirectoryIterator = new RecursiveDirectoryIterator($pathToDirOrFile);
            $recursiveDirectoryFilterIterator = new RecursiveDirectoryFilterIterator($recursiveDirectoryIterator, $excludedPaths);
            $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryFilterIterator);
            $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.(ya?ml(\.dist)?)$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($regexIterator as $pathToFile) {
                $pathToFiles[] = reset($pathToFile);
            }
        }

        $pathToFiles = str_replace('\\', '/', $pathToFiles);

        return array_unique($pathToFiles);
    }
}
