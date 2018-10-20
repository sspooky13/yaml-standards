<?php

namespace YamlAlphabeticalChecker;

class ProcessOutput
{
    const
        STATUS_CODE_OK = 0,
        STATUS_CODE_INVALID_SORT = 1,
        STATUS_CODE_SKIPP = 2,
        STATUS_CODE_ERROR = 3;

    private static $statusMap = [
        self::STATUS_CODE_OK => ['symbol' => '.', 'format' => '%s', 'description' => 'OK'],
        self::STATUS_CODE_INVALID_SORT => ['symbol' => 'I', 'format' => '<fg=red>%s</fg=red>', 'description' => 'Invalid file sort'],
        self::STATUS_CODE_SKIPP => ['symbol' => 'S', 'format' => '<fg=cyan>%s</fg=cyan>', 'description' => 'Skipped'],
        self::STATUS_CODE_ERROR => ['symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'Error'],
    ];

    public static $statusCodeByResultCode = [
        Result::RESULT_CODE_OK => self::STATUS_CODE_OK,
        Result::RESULT_CODE_INVALID_SORT => self::STATUS_CODE_INVALID_SORT,
        Result::RESULT_CODE_GENERAL_ERROR => self::STATUS_CODE_ERROR,
    ];

    private $currentPosition = 0;
    private $progressLine;
    private $countOfFiles;

    /**
     * @param int $countOfFiles
     */
    public function __construct($countOfFiles)
    {
        $this->countOfFiles = $countOfFiles;

        $progressLine = [];

        // create empty spaces for summary in end
        for ($i = 0; $i <= $countOfFiles + 10; $i++) {
            $progressLine[] = ' ';
        }
        $this->progressLine = $progressLine;
    }

    /**
     * @param int $statusCode
     * @return string
     */
    public function process($statusCode)
    {
        $symbol = sprintf(self::$statusMap[$statusCode]['format'], self::$statusMap[$statusCode]['symbol']);
        $this->progressLine[$this->currentPosition] = $symbol;
        $this->currentPosition++;

        $percentOfComplete = $this->currentPosition * 100 / $this->countOfFiles;

        end($this->progressLine);
        $lastKey = key($this->progressLine);
        $this->progressLine[$lastKey] = sprintf('%d/%d (%d%%)', $this->currentPosition, $this->countOfFiles, $percentOfComplete);
        $progressLine = implode('', $this->progressLine);

        return "\r" . $progressLine;
    }

    /**
     * @return string
     */
    public function getLegend()
    {
        $symbols = [];

        foreach (self::$statusMap as $status) {
            $symbol = $status['symbol'];
            $format = $status['format'];
            $description = $status['description'];

            $symbols[$symbol] = sprintf('%s-%s', sprintf($format, $symbol), $description);
        }

        return sprintf("\nLegend: %s\n", implode(', ', $symbols));
    }
}
