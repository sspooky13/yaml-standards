<?php

namespace YamlStandards\Command\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Symfony\Component\Console\Output\OutputInterface;

class YamlFilesPathService
{
    /**
     * @param string[] $pathToDirsOrFiles
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return string[]
     */
    public static function getPathToYamlFiles(array $pathToDirsOrFiles, OutputInterface $output)
    {
        $pathToFiles = [];
        foreach ($pathToDirsOrFiles as $pathToDirOrFile) {
            if (self::existsDirectoryOrFile($pathToDirOrFile) === false) {
                continue;
            }

            if (is_file($pathToDirOrFile)) {
                $pathToFiles[] = $pathToDirOrFile;
                continue;
            }

            $recursiveIteratorIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($pathToDirOrFile),
                RecursiveIteratorIterator::LEAVES_ONLY,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.(ya?ml(\.dist)?)$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($regexIterator as $pathToFile) {
                $pathToFiles[] = reset($pathToFile);
            }
        }

        $pathToFiles = str_replace('\\', '/', $pathToFiles);

        return array_unique($pathToFiles);
    }

    /**
     * @param string $pathToDirOrFile
     * @return bool
     */
    private static function existsDirectoryOrFile($pathToDirOrFile)
    {
        return is_dir($pathToDirOrFile) || is_file($pathToDirOrFile);
    }
}
