<?php

namespace YamlAlphabeticalChecker;

class Reporting
{
    private static $startTime;

    public static function startTiming()
    {
        self::$startTime = microtime(true);
    }

    /**
     * @return string
     */
    public static function printRunTime()
    {
        $time = round((microtime(true) - self::$startTime) * 1000);
        $memory = round(memory_get_peak_usage(true) / (1024 * 1024), 2);

        return PHP_EOL . sprintf('Time: %dms; Memory: %.2fMb', $time, $memory) . PHP_EOL;
    }
}
