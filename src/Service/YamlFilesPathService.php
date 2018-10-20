<?php

namespace YamlAlphabeticalChecker\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class YamlFilesPathService
{
    /**
     * @param string[] $pathToDirsOrFiles
     * @return string[]
     */
    public static function getPathToYamlFiles(array $pathToDirsOrFiles)
    {
        $pathToFiles = [];
        foreach ($pathToDirsOrFiles as $pathToDirOrFile) {
            if (is_file($pathToDirOrFile)) {
                $pathToFiles[] = $pathToDirOrFile;
                continue;
            }

            $recursiveDirectoryIterator = new RecursiveDirectoryIterator($pathToDirOrFile);
            $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);
            $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.(yml|yaml|yaml\.dist|yml\.dist)$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($regexIterator as $pathToFile) {
                $pathToFiles[] = reset($pathToFile);
            }
        }

        $pathToFiles = str_replace('\\', '/', $pathToFiles);

        return array_unique($pathToFiles);
    }
}
