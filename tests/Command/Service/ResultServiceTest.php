<?php

declare(strict_types=1);

namespace YamlStandards\Command\Service;

use PHPUnit\Framework\TestCase;
use YamlStandards\Result\Result;

class ResultServiceTest extends TestCase
{
    public function testReturnHighestResultCode(): void
    {
        $result1 = new Result('pathToFirstFile', Result::RESULT_CODE_OK);
        $result2 = new Result('pathToSecondFile', Result::RESULT_CODE_INVALID_FILE_SYNTAX);
        $result3 = new Result('pathToThirdFile', Result::RESULT_CODE_GENERAL_ERROR);
        $results = [
            $result1,
            $result2,
            $result3,
        ];
        $resultCode = ResultService::getResultCodeByResults($results);

        $this->assertSame((int)Result::RESULT_CODE_GENERAL_ERROR, $resultCode);
    }

    public function testFixedFileReturnsOkResultCode(): void
    {
        $result = new Result('pathToFirstFile', Result::RESULT_CODE_FIXED_INVALID_FILE_SYNTAX);
        $resultCode = ResultService::getResultCodeByResults([$result]);

        $this->assertSame((int)Result::RESULT_CODE_OK, $resultCode);
    }
}
