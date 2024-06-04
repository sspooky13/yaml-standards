<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument\resource\TestService;

class AnotherTestService
{
    /**
     * @var object
     */
    private $service1;

    /**
     * @var object
     */
    private $service2;

    /**
     * @var object
     */
    private $service3;

    public function __construct(object $service1, object $service2, object $service3)
    {
        $this->service1 = $service1;
        $this->service2 = $service2;
        $this->service3 = $service3;
    }
}
