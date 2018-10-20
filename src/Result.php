<?php

namespace YamlAlphabeticalChecker;

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
     * @var string|null
     */
    private $message;

    /**
     * @param string $pathToFile
     * @param int $resultCode
     * @param string|null $message
     */
    public function __construct(
        $pathToFile,
        $resultCode,
        $message = null
    ) {
        $this->pathToFile = $pathToFile;
        $this->resultCode = $resultCode;
        $this->message = $message;
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
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }
}
