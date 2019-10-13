<?php

declare(strict_types=1);

namespace YamlStandards\Result;

class Result
{
    public const
        RESULT_CODE_OK = 0,
        RESULT_CODE_INVALID_FILE_SYNTAX = 1,
        RESULT_CODE_GENERAL_ERROR = 2;

    /**
     * string
     */
    private $pathToFile;

    /**
     * int
     */
    private $resultCode;

    /**
     * @var int
     */
    private $statusCode;

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
     * @param int $resultCode
     * @param int $statusCode
     * @param string|null $message
     * @param bool $canBeFixedByFixer
     */
    public function __construct(
        string $pathToFile,
        int $resultCode,
        int $statusCode,
        ?string $message = null,
        bool $canBeFixedByFixer = false
    ) {
        $this->pathToFile = $pathToFile;
        $this->resultCode = $resultCode;
        $this->statusCode = $statusCode;
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
     * @return int
     */
    public function getResultCode(): int
    {
        return $this->resultCode;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
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
