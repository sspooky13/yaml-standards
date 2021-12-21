<?php

declare(strict_types=1);

namespace YamlStandards\Model\Component\Cache;

interface CacheInterface
{
    /**
     * @param string[] $pathToFiles
     * @param int $configNumber
     * @param string $pathToCacheDir
     * @return string[]
     */
    public function getCachedPathToFiles(array $pathToFiles, int $configNumber, string $pathToCacheDir): array;

    /**
     * @param string[] $pathToFiles
     * @param int $configNumber
     * @param string $pathToCacheDir
     */
    public function cacheFiles(array $pathToFiles, int $configNumber, string $pathToCacheDir): void;

    /**
     * @param string $pathToConfigFile
     * @param string $pathToCacheDir
     */
    public function deleteCacheFileIfConfigFileWasChanged(string $pathToConfigFile, string $pathToCacheDir): void;
}
