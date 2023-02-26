<?php

declare(strict_types=1);

namespace YamlStandards\Result;

class Result
{
    public const
        RESULT_CODE_OK = 0.0,
        RESULT_CODE_FIXED_INVALID_FILE_SYNTAX = 0.1,
        RESULT_CODE_INVALID_FILE_SYNTAX = 1.0,
        RESULT_CODE_GENERAL_ERROR = 2.0;
    public const RESULT_CODE_OK_AS_INTEGER = 0;

    /**
     * @var string
     */
    private $pathToFile;

    /**
     * @var float
     */
    private $resultCode;

    /**
     * @var string|null
     */
    private $message;

    /**
     * @var bool
     */
    private $canBeFixedByFixer;

    /**
     * @param string $pathToFile
     * @param float $resultCode
     * @param string|null $message
     * @param bool $canBeFixedByFixer
     */
    public function __construct(
        string $pathToFile,
        float $resultCode,
        ?string $message = null,
        bool $canBeFixedByFixer = false
    ) {
        $this->pathToFile = $pathToFile;
        $this->resultCode = $resultCode;
        $this->message = $message;
        $this->canBeFixedByFixer = $canBeFixedByFixer;
    }

    /**
     * @return string
     */
    public function getPathToFile(): string
    {
        return $this->pathToFile;
    }

    /**
     * @return float
     */
    public function getResultCode(): float
    {
        return $this->resultCode;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function canBeFixedByFixer(): bool
    {
        return $this->canBeFixedByFixer;
    }
}
