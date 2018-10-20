<?php

namespace YamlStandards\Service;

class ResultService
{
    /**
     * @param \YamlStandards\Result[] $results
     * @return int
     */
    public static function getResultCodeByResults(array $results)
    {
        $resultCode = 0;

        foreach ($results as $result) {
            $resultCode = $result->getResultCode() > $resultCode ? $result->getResultCode() : $resultCode;
        }

        return $resultCode;
    }
}
