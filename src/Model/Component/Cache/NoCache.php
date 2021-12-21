<?php

declare(strict_types = 1);

namespace YamlStandards\Model\Component\Cache;

class NoCache implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function getCachedPathToFiles(array $pathToFiles, int $configNumber, string $pathToCacheDir): array
    {
        return $pathToFiles;
    }

    /**
     * @inheritDoc
     */
    public function cacheFiles(array $pathToFiles, int $configNumber, string $pathToCacheDir): void
    {
        // do nothing
    }

    /**
     * @inheritDoc
     */
    public function deleteCacheFileIfConfigFileWasChanged(string $pathToConfigFile, string $pathToCacheDir): void
    {
        // do nothing
    }
}
