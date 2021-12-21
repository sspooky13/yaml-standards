<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

class FilesPathService
{
    /**
     * @param string[] $patterns
     * @return string[]
     */
    public static function getPathToFiles(array $patterns): array
    {
        $pathToFiles = [[]];
        foreach ($patterns as $pattern) {
            if (is_file($pattern)) {
                $pathToFiles[] = [$pattern];
            } else {
                $pathToFiles[] = self::globRecursive($pattern);
            }
        }

        $pathToFiles = array_merge(...$pathToFiles);
        $pathToFiles = str_replace('\\', '/', $pathToFiles);

        return array_unique($pathToFiles);
    }

    /**
     * @param string $pattern
     * @return string[]
     *
     * @link https://www.php.net/manual/en/function.glob.php#106595
     */
    private static function globRecursive(string $pattern): array
    {
        $pathNames = glob($pattern, GLOB_BRACE);
        $files = array_filter($pathNames, 'is_file');

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::globRecursive($dir . '/' . basename($pattern)));
        }

        return $files;
    }
}
