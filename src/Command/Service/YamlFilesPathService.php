<?php

namespace YamlStandards\Command\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

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

            try {
                $recursiveDirectoryIterator = new RecursiveDirectoryIterator($pathToDirOrFile);
                $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);
                $regexIterator = new RegexIterator($recursiveIteratorIterator, '/^.+\.(ya?ml(\.dist)?)$/i', RecursiveRegexIterator::GET_MATCH);

                foreach ($regexIterator as $pathToFile) {
                    $pathToFiles[] = reset($pathToFile);
                }
            } catch (UnexpectedValueException $exception) {
                $output->writeln(sprintf('Error was caught: %s' . PHP_EOL, $exception->getMessage()));
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
