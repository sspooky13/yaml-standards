<?php

declare(strict_types=1);

namespace YamlStandards\Model\YamlServiceArgument\resource\TestService;

class BlockMapTestService
{
    /**
     * @var array
     */
    private $filesystems;

    /**
     * @param array $filesystems
     */
    public function __construct(array $filesystems)
    {
        $this->filesystems = $filesystems;
    }
}
