<?php

namespace YamlAlphabeticalChecker;

class Result
{
    const
        RESULT_CODE_OK = 0,
        RESULT_CODE_INVALID_SORT = 1,
        RESULT_CODE_GENERAL_ERROR = 2;

    /**
     * string|null
     */
    private $pathToFile;

    /**
     * @var string
     */
    private $message;

    /**
     * int|null
     */
    private $resultCode;

    public function __construct(
        $pathToFile,
        $message,
        $resultCode = self::RESULT_CODE_OK
    ) {
        $this->pathToFile = $pathToFile;
        $this->message = $message;
        $this->resultCode = $resultCode;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string|null
     */
    public function getPathToFile()
    {
        return $this->pathToFile;
    }

    /**
     * @return int|null
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }
}
