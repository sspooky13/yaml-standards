<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument\resource\TestService;

class FlowMapTestService
{
    /**
     * @var mixed
     */
    private $firstParameter;

    /**
     * @var object
     */
    private $container;

    /**
     * @var array
     */
    private $validatorMap;

    /**
     * @param mixed $firstParameter
     * @param object $container
     * @param array $validatorMap
     */
    public function __construct($firstParameter, object $container, array $validatorMap)
    {
        $this->firstParameter = $firstParameter;
        $this->container = $container;
        $this->validatorMap = $validatorMap;
    }
}
