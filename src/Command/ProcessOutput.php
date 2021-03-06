<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use Symfony\Component\Console\Terminal;
use YamlStandards\Result\Result;

class ProcessOutput
{
    public const
        STATUS_CODE_OK = 0,
        STATUS_CODE_INVALID_FILE_SYNTAX = 1,
        STATUS_CODE_SKIPP = 2,
        STATUS_CODE_ERROR = 3;

    private const
        STATUS_SYMBOL = 'symbol',
        STATUS_FORMAT = 'format',
        STATUS_DESCRIPTION = 'description';

    private const STATUS_MAP = [
        self::STATUS_CODE_OK => [self::STATUS_SYMBOL => '.', self::STATUS_FORMAT => '%s', self::STATUS_DESCRIPTION => 'OK'],
        self::STATUS_CODE_INVALID_FILE_SYNTAX => [self::STATUS_SYMBOL => 'I', self::STATUS_FORMAT => '<fg=red>%s</fg=red>', self::STATUS_DESCRIPTION => 'Invalid file syntax'],
        self::STATUS_CODE_SKIPP => [self::STATUS_SYMBOL => 'S', self::STATUS_FORMAT => '<fg=cyan>%s</fg=cyan>', self::STATUS_DESCRIPTION => 'Skipped'],
        self::STATUS_CODE_ERROR => [self::STATUS_SYMBOL => 'E', self::STATUS_FORMAT => '<bg=red>%s</bg=red>', self::STATUS_DESCRIPTION => 'Error'],
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
    public function __construct(int $countOfFiles)
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
    public function process(int $statusCode): string
    {
        $symbol = sprintf(self::STATUS_MAP[$statusCode][self::STATUS_FORMAT], self::STATUS_MAP[$statusCode][self::STATUS_SYMBOL]);
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
    public function getLegend(): string
    {
        $symbols = [];

        foreach (self::STATUS_MAP as $status) {
            $symbol = $status[self::STATUS_SYMBOL];
            $format = $status[self::STATUS_FORMAT];
            $description = $status[self::STATUS_DESCRIPTION];

            $symbols[$symbol] = sprintf('%s-%s', sprintf($format, $symbol), $description);
        }

        return sprintf("\nLegend: %s\n", implode(', ', $symbols));
    }
}
