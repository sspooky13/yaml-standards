<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

class StandardParametersData
{
    /**
     * @var int|null
     */
    private $depth;

    /**
     * @var int|null
     */
    private $indents;

    /**
     * @var int|null
     */
    private $level;

    /**
     * @var string|null
     */
    private $serviceAliasingType;

    /**
     * @param int|null $depth
     * @param int|null $indents
     * @param int|null $level
     * @param string|null $serviceAliasingType
     */
    public function __construct(?int $depth, ?int $indents, ?int $level, ?string $serviceAliasingType)
    {
        $this->depth = $depth;
        $this->indents = $indents;
        $this->level = $level;
        $this->serviceAliasingType = $serviceAliasingType;
    }

    /**
     * @return int|null
     */
    public function getDepth(): ?int
    {
        return $this->depth;
    }

    /**
     * @return int|null
     */
    public function getIndents(): ?int
    {
        return $this->indents;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @return string|null
     */
    public function getServiceAliasingType(): ?string
    {
        return $this->serviceAliasingType;
    }
}
