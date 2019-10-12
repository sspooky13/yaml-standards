<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

use YamlStandards\Command\ProcessOutput;

class ProcessOutputService
{
    /**
     * @param \YamlStandards\Result\Result[] $results
     * @return int
     */
    public static function getWorstStatusCodeByResults(array $results)
    {
        $resultCode = ProcessOutput::STATUS_CODE_OK;

        foreach ($results as $result) {
            $statusCode = $result->getStatusCode();
            $resultCode = $statusCode > $resultCode ? $statusCode : $resultCode;
        }

        return $resultCode;
    }
}
