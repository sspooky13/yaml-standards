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
     * @param int|null $depth
     * @param int|null $indents
     * @param int|null $level
     */
    public function __construct(?int $depth, ?int $indents, ?int $level)
    {
        $this->depth = $depth;
        $this->indents = $indents;
        $this->level = $level;
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
}
