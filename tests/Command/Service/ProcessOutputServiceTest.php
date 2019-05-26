<?php

namespace YamlStandards\Command\Service;

use PHPUnit\Framework\TestCase;
use YamlStandards\Result;

class ProcessOutputServiceTest extends TestCase
{
    public function testReturnOkStatusCode()
    {
        $result1 = new Result('pathToFirstFile', Result::RESULT_CODE_OK);
        $result2 = new Result('pathToSecondFile', Result::RESULT_CODE_OK);
        $result3 = new Result('pathToThirdFile', Result::RESULT_CODE_OK);
        $results = [
            $result1,
            $result2,
            $result3,
        ];
        $resultCode = ProcessOutputService::getWorstStatusCodeByResults($results);

        $this->assertSame(0, $resultCode);
    }

    public function testReturnErrorStatusCode()
    {
        $result1 = new Result('pathToFirstFile', Result::RESULT_CODE_OK);
        $result2 = new Result('pathToSecondFile', Result::RESULT_CODE_INVALID_FILE_SYNTAX);
        $result3 = new Result('pathToThirdFile', Result::RESULT_CODE_GENERAL_ERROR);
        $results = [
            $result1,
            $result2,
            $result3,
        ];
        $resultCode = ProcessOutputService::getWorstStatusCodeByResults($results);

        $this->assertSame(3, $resultCode);
    }
}
