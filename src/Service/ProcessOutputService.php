<?php

namespace YamlAlphabeticalChecker\Service;

use YamlAlphabeticalChecker\ProcessOutput;

class ProcessOutputService
{
    /**
     * @param \YamlAlphabeticalChecker\Result[] $results
     * @return int
     */
    public static function getWorstStatusCodeByResults(array $results)
    {
        $resultCode = ProcessOutput::STATUS_CODE_OK;

        foreach ($results as $result) {
            $statusCode = ProcessOutput::$statusCodeByResultCode[$result->getResultCode()];
            $resultCode = $statusCode > $resultCode ? $statusCode : $resultCode;
        }

        return $resultCode;
    }
}
