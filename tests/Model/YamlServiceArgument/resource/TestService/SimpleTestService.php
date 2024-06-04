<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument\resource\TestService;

class SimpleTestService
{
    /**
     * @var string
     */
    private $param1;

    /**
     * @var string
     */
    private $param2;

    /**
     * @var int
     */
    private $param3;

    public function __construct(string $param1, string $param2, int $param3)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
    }
}
