<?php

declare(strict_types = 1);

namespace YamlStandards\Model\Component\Cache;

use PHPUnit\Framework\TestCase;

class NativeCacheTest extends TestCase
{
    private const PATH_TO_FIRST_FILE = __DIR__ . '/resource/file1.yaml';
    private const PATH_TO_SECOND_FILE = __DIR__ . '/resource/file2.yaml';
    private const PATH_TO_THIRD_FILE = __DIR__ . '/resource/file3.yaml';
    private const PATH_TO_FOURTH_FILE = __DIR__ . '/resource/file4.yaml';
    private const PATH_TO_FIFTH_FILE = __DIR__ . '/resource/file5.yaml';

    private const PATH_TO_TEMP_FIRST_FILE = __DIR__ . '/resource/temp/file1.yaml';
    private const PATH_TO_TEMP_SECOND_FILE = __DIR__ . '/resource/temp/file2.yaml';
    private const PATH_TO_TEMP_THIRD_FILE = __DIR__ . '/resource/temp/file3.yaml';
    private const PATH_TO_TEMP_FOURTH_FILE = __DIR__ . '/resource/temp/file4.yaml';
    private const PATH_TO_TEMP_FIFTH_FILE = __DIR__ . '/resource/temp/file5.yaml';

    private const PATH_TO_CACHE_DIRECTORY = __DIR__ . '/resource/temp/';
    private const PATH_TO_CACHE_FILE = self::PATH_TO_CACHE_DIRECTORY . NativeCache::CACHE_FILE_NAME;

    protected function setUp(): void
    {
        @unlink(self::PATH_TO_CACHE_FILE);
        @unlink(self::PATH_TO_TEMP_FIRST_FILE);
        @unlink(self::PATH_TO_TEMP_SECOND_FILE);
        @unlink(self::PATH_TO_TEMP_THIRD_FILE);
        @unlink(self::PATH_TO_TEMP_FOURTH_FILE);
        @unlink(self::PATH_TO_TEMP_FIFTH_FILE);

        copy(self::PATH_TO_FIRST_FILE, self::PATH_TO_TEMP_FIRST_FILE);
        copy(self::PATH_TO_SECOND_FILE, self::PATH_TO_TEMP_SECOND_FILE);
        copy(self::PATH_TO_THIRD_FILE, self::PATH_TO_TEMP_THIRD_FILE);
        copy(self::PATH_TO_FOURTH_FILE, self::PATH_TO_TEMP_FOURTH_FILE);
        copy(self::PATH_TO_FIFTH_FILE, self::PATH_TO_TEMP_FIFTH_FILE);
    }

    public function testGetOnlyChangedFile(): void
    {
        $firstFile = self::PATH_TO_TEMP_FIRST_FILE;
        $secondFile = self::PATH_TO_TEMP_SECOND_FILE;
        $thirdFile = self::PATH_TO_TEMP_THIRD_FILE;

        $nativeCache = new NativeCache();
        $nativeCache->cacheFiles([$firstFile, $secondFile, $thirdFile], 1, self::PATH_TO_CACHE_DIRECTORY);

        sleep(1);
        file_put_contents($secondFile, PHP_EOL, FILE_APPEND);

        $changedFile = $nativeCache->getCachedPathToFiles([$firstFile, $secondFile, $thirdFile], 1, self::PATH_TO_CACHE_DIRECTORY);

        self::assertSame([$secondFile], $changedFile);
    }

    public function testGetFilesThatWereNotCached(): void
    {
        $firstFile = self::PATH_TO_TEMP_FIRST_FILE;
        $secondFile = self::PATH_TO_TEMP_SECOND_FILE;
        $thirdFile = self::PATH_TO_TEMP_THIRD_FILE;

        $nativeCache = new NativeCache();
        $nativeCache->cacheFiles([$firstFile, $thirdFile], 1, self::PATH_TO_CACHE_DIRECTORY);

        $changedFile = $nativeCache->getCachedPathToFiles([$firstFile, $secondFile, $thirdFile], 1, self::PATH_TO_CACHE_DIRECTORY);

        self::assertSame([$secondFile], $changedFile);
    }

    public function testGetsSameCachedFilesAfterTwiceCall(): void
    {
        $firstFile = self::PATH_TO_TEMP_FIRST_FILE;
        $secondFile = self::PATH_TO_TEMP_SECOND_FILE;
        $thirdFile = self::PATH_TO_TEMP_THIRD_FILE;

        $nativeCache = new NativeCache();
        $nativeCache->cacheFiles([$firstFile], 1, self::PATH_TO_CACHE_DIRECTORY);

        $changedFile = $nativeCache->getCachedPathToFiles([$firstFile, $secondFile, $thirdFile], 1, self::PATH_TO_CACHE_DIRECTORY);
        $changedFile2 = $nativeCache->getCachedPathToFiles([$firstFile, $secondFile, $thirdFile], 1, self::PATH_TO_CACHE_DIRECTORY);

        self::assertSame($changedFile, $changedFile2);
    }

    public function testSaveFilesToCache(): void
    {
        $firstFiles = [
            self::PATH_TO_TEMP_FIRST_FILE,
            self::PATH_TO_TEMP_SECOND_FILE,
        ];
        $secondFiles = [
            self::PATH_TO_TEMP_THIRD_FILE,
        ];
        $thirdFiles = [
            self::PATH_TO_TEMP_FOURTH_FILE,
            self::PATH_TO_TEMP_FIFTH_FILE,
        ];

        $nativeCache = new NativeCache();
        $nativeCache->cacheFiles($firstFiles, 1, self::PATH_TO_CACHE_DIRECTORY);
        $nativeCache->cacheFiles($secondFiles, 2, self::PATH_TO_CACHE_DIRECTORY);
        $nativeCache->cacheFiles($thirdFiles, 3, self::PATH_TO_CACHE_DIRECTORY);

        $cacheFileContent = unserialize(file_get_contents(self::PATH_TO_CACHE_FILE));

        self::assertSame(count(array_merge(...$cacheFileContent)), count($firstFiles) + count($secondFiles) + count($thirdFiles));
    }

    public function testCacheFileWasDeletedAfterChangeConfigFile(): void
    {
        $configFile = self::PATH_TO_TEMP_FIRST_FILE;
        $configNumber = NativeCache::CONFIG_NUMBER_FOR_CONFIG_FILE;
        $pathToCacheDir = self::PATH_TO_CACHE_DIRECTORY;

        $nativeCache = new NativeCache();
        $nativeCache->cacheFiles([$configFile], $configNumber, $pathToCacheDir);
        self::assertSame(count($nativeCache->getCachedPathToFiles([$configFile], $configNumber, $pathToCacheDir)), 0, 'Config file was not cached');

        $oldCacheFileContent = file_get_contents(self::PATH_TO_CACHE_FILE);
        sleep(1);
        file_put_contents($configFile, PHP_EOL, FILE_APPEND);
        $nativeCache->deleteCacheFileIfConfigFileWasChanged($configFile, $pathToCacheDir);

        $newCacheFileContent = file_get_contents(self::PATH_TO_CACHE_FILE);
        self::assertNotSame($newCacheFileContent, $oldCacheFileContent, 'Cache file after change config file was not deleted (content is same)');
    }
}
