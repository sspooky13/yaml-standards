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
     * @return array
     */
    public static function getPathToYamlFiles(array $pathToDirs)
    {
        $fileList = [];
        foreach ($pathToDirs as $pathToDir) {
            $recursiveDirectoryIterator = new RecursiveDirectoryIterator($pathToDir);
            $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);
            $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.yml$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($regexIterator as $pathToFile) {
                $fileList[] = reset($pathToFile);
            }
        }

        return $fileList;
    }
}
