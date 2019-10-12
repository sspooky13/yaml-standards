<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use Symfony\Component\Console\Terminal;
use YamlStandards\Result\Result;

class ProcessOutput
{
    const
        STATUS_CODE_OK = 0,
        STATUS_CODE_INVALID_FILE_SYNTAX = 1,
        STATUS_CODE_SKIPP = 2,
        STATUS_CODE_ERROR = 3;

    private static $statusMap = [
        self::STATUS_CODE_OK => ['symbol' => '.', 'format' => '%s', 'description' => 'OK'],
        self::STATUS_CODE_INVALID_FILE_SYNTAX => ['symbol' => 'I', 'format' => '<fg=red>%s</fg=red>', 'description' => 'Invalid file syntax'],
        self::STATUS_CODE_SKIPP => ['symbol' => 'S', 'format' => '<fg=cyan>%s</fg=cyan>', 'description' => 'Skipped'],
        self::STATUS_CODE_ERROR => ['symbol' => 'E', 'format' => '<bg=red>%s</bg=red>', 'description' => 'Error'],
    ];

    /**
     * @var int
     */
    private $countOfFiles;

    /**
     * @var int
     */
    private $maxLineWidth;

    /**
     * @var int
     */
    private $progressLength;

    /**
     * @var string[]
     */
    private $progressLine;

    /**
     * @param int $countOfFiles
     */
    public function __construct($countOfFiles)
    {
        $this->countOfFiles = $countOfFiles;
        $this->progressLine = [];
        $this->progressLength = strlen(sprintf('%d/%1$d (%3d%%)', $this->countOfFiles, 100)) + 2;
        $this->maxLineWidth = (new Terminal())->getWidth() - $this->progressLength - 1; // -1 because result is escaped after every file in windows
    }

    /**
     * @param int $statusCode
     * @return string
     */
    public function process($statusCode)
    {
        $symbol = sprintf(self::$statusMap[$statusCode]['format'], self::$statusMap[$statusCode]['symbol']);
        $this->progressLine[] = $symbol;
        $currentPosition = count($this->progressLine);

        $percentOfComplete = $currentPosition * 100 / $this->countOfFiles;

        $progress = sprintf('%d/%d (%3d%%)', $currentPosition, $this->countOfFiles, $percentOfComplete);
        $wrappedProgressLines = array_chunk($this->progressLine, $this->maxLineWidth);
        $wrapLine = ($currentPosition % $this->maxLineWidth) === 0 ? PHP_EOL : '';
        $currentLineResults = end($wrappedProgressLines);
        $spaceForSymbols = str_repeat(' ', $this->maxLineWidth - count($currentLineResults));
        $progressLineFormat = "\r%-s%-s%+" . $this->progressLength . 's%s';

        return sprintf($progressLineFormat, implode('', $currentLineResults), $spaceForSymbols, $progress, $wrapLine);
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
