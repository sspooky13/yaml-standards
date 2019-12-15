<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

class StandardParametersData
{
    /**
     * @var int
     */
    private $depth;

    /**
     * @var int
     */
    private $indents;

    /**
     * @var int
     */
    private $level;

    /**
     * @var string
     */
    private $serviceAliasingType;

    /**
     * @param int $depth
     * @param int $indents
     * @param int $level
     * @param string $serviceAliasingType
     */
    public function __construct(int $depth, int $indents, int $level, string $serviceAliasingType)
    {
        $this->depth = $depth;
        $this->indents = $indents;
        $this->level = $level;
        $this->serviceAliasingType = $serviceAliasingType;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @return int
     */
    public function getIndents(): int
    {
        return $this->indents;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getServiceAliasingType(): string
    {
        return $this->serviceAliasingType;
    }
}
