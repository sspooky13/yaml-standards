<?php

namespace YamlStandards\Service;

use YamlStandards\ProcessOutput;

class ProcessOutputService
{
    /**
     * @param \YamlStandards\Result[] $results
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
