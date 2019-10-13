<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

use PHPUnit\Framework\TestCase;
use YamlStandards\Command\ProcessOutput;
use YamlStandards\Result\Result;

class ProcessOutputServiceTest extends TestCase
{
    public function testReturnOkStatusCode(): void
    {
        $result1 = new Result('pathToFirstFile', Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        $result2 = new Result('pathToSecondFile', Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        $result3 = new Result('pathToThirdFile', Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        $results = [
            $result1,
            $result2,
            $result3,
        ];
        $resultCode = ProcessOutputService::getWorstStatusCodeByResults($results);

        $this->assertSame(ProcessOutput::STATUS_CODE_OK, $resultCode);
    }

    public function testReturnErrorStatusCode(): void
    {
        $result1 = new Result('pathToFirstFile', Result::RESULT_CODE_OK, ProcessOutput::STATUS_CODE_OK);
        $result2 = new Result('pathToSecondFile', Result::RESULT_CODE_INVALID_FILE_SYNTAX, ProcessOutput::STATUS_CODE_INVALID_FILE_SYNTAX);
        $result3 = new Result('pathToThirdFile', Result::RESULT_CODE_GENERAL_ERROR, ProcessOutput::STATUS_CODE_ERROR);
        $results = [
            $result1,
            $result2,
            $result3,
        ];
        $resultCode = ProcessOutputService::getWorstStatusCodeByResults($results);

        $this->assertSame(ProcessOutput::STATUS_CODE_ERROR, $resultCode);
    }
}
