<?php

namespace YamlStandards\Result;

class Result
{
    const
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
        $pathToFile,
        $resultCode,
        $statusCode,
        $message = null,
        $canBeFixedByFixer = false
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
    public function getPathToFile()
    {
        return $this->pathToFile;
    }

    /**
     * @return int
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function canBeFixedByFixer()
    {
        return $this->canBeFixedByFixer;
    }
}
