<?php

declare(strict_types = 1);

namespace YamlStandards\Model\Component\Cache;

use YamlStandards\Model\Component\Cache\Exception\CustomPathToCacheDirectoryNotFound;

class NativeCache implements CacheInterface
{
    public const CACHE_FILE_NAME = 'yaml-standards.cache';
    public const CONFIG_NUMBER_FOR_CONFIG_FILE = 0;

    /**
     * @inheritDoc
     *
     * @throws \YamlStandards\Model\Component\Cache\Exception\CustomPathToCacheDirectoryNotFound
     */
    public function getCachedPathToFiles(array $pathToFiles, int $configNumber, string $pathToCacheDir): array
    {
        if (is_dir($pathToCacheDir) === false) {
            throw new CustomPathToCacheDirectoryNotFound(sprintf('Custom path to cache directory %s was not found', $pathToCacheDir));
        }

        if (file_exists($pathToCacheDir . self::CACHE_FILE_NAME) === false) {
            return $pathToFiles;
        }

        $cacheFileContent = file_get_contents($pathToCacheDir . self::CACHE_FILE_NAME);
        $filesModifiedTimeIndexedByPathToFileAndConfigNumber = unserialize($cacheFileContent, ['allowed_classes' => false]);

        if (array_key_exists($configNumber, $filesModifiedTimeIndexedByPathToFileAndConfigNumber) === false) {
            return $pathToFiles;
        }

        $changedFiles = [];
        foreach ($pathToFiles as $pathToFile) {
            if (array_key_exists($pathToFile, $filesModifiedTimeIndexedByPathToFileAndConfigNumber[$configNumber])) {
                if (filemtime($pathToFile) !== $filesModifiedTimeIndexedByPathToFileAndConfigNumber[$configNumber][$pathToFile]) {
                    $changedFiles[] = $pathToFile;
                }
            } else {
                $changedFiles[] = $pathToFile;
            }
        }

        return $changedFiles;
    }

    /**
     * @inheritDoc
     *
     * @throws \YamlStandards\Model\Component\Cache\Exception\CustomPathToCacheDirectoryNotFound
     */
    public function cacheFiles(array $pathToFiles, int $configNumber, string $pathToCacheDir): void
    {
        $filesModifiedTimeIndexedByPathToFileAndConfigNumber = [[]];

        if (is_dir($pathToCacheDir) === false) {
            throw new CustomPathToCacheDirectoryNotFound(sprintf('Custom path to cache directory %s was not found', $pathToCacheDir));
        }

        if (file_exists($pathToCacheDir . self::CACHE_FILE_NAME)) {
            $filesModifiedTimeIndexedByPathToFileAndConfigNumber = unserialize(file_get_contents($pathToCacheDir . self::CACHE_FILE_NAME), ['allowed_classes' => false]);
        }

        foreach ($pathToFiles as $pathToFile) {
            $filesModifiedTimeIndexedByPathToFileAndConfigNumber[$configNumber][$pathToFile] = filemtime($pathToFile);
        }

        file_put_contents($pathToCacheDir . self::CACHE_FILE_NAME, serialize($filesModifiedTimeIndexedByPathToFileAndConfigNumber));
    }

    /**
     * @inheritDoc
     *
     * @throws \YamlStandards\Model\Component\Cache\Exception\CustomPathToCacheDirectoryNotFound
     */
    public function deleteCacheFileIfConfigFileWasChanged(string $pathToConfigFile, string $pathToCacheDir): void
    {
        if (count($this->getCachedPathToFiles([$pathToConfigFile], self::CONFIG_NUMBER_FOR_CONFIG_FILE, $pathToCacheDir)) > 0) {
            if (file_exists($pathToCacheDir . self::CACHE_FILE_NAME)) {
                unlink($pathToCacheDir . self::CACHE_FILE_NAME);
            }
            $this->cacheFiles([$pathToConfigFile], self::CONFIG_NUMBER_FOR_CONFIG_FILE, $pathToCacheDir);
        }
    }
}
