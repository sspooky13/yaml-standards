<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument\resource\TestService;

class PartialArgumentsTestService
{
    /**
     * @var object
     */
    private $autowiredService;

    /**
     * @var object
     */
    private $anotherAutowiredService;

    /**
     * @var object
     */
    private $manuallyWiredService;

    /**
     * @var object
     */
    private $anotherManuallyWiredService;

    /**
     * @param object $autowiredService
     * @param object $anotherAutowiredService
     * @param object $manuallyWiredService
     * @param object $anotherManuallyWiredService
     */
    public function __construct(object $autowiredService, object $anotherAutowiredService, object $manuallyWiredService, object $anotherManuallyWiredService)
    {
        $this->autowiredService = $autowiredService;
        $this->anotherAutowiredService = $anotherAutowiredService;
        $this->manuallyWiredService = $manuallyWiredService;
        $this->anotherManuallyWiredService = $anotherManuallyWiredService;
    }
}
