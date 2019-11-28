<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

use YamlStandards\Model\CheckerInterface;
use YamlStandards\Model\FixerInterface;

class YamlStandardConfigSingleStandardData
{
    /**
     * @var \YamlStandards\Model\CheckerInterface
     */
    private $checker;

    /**
     * @var \YamlStandards\Model\FixerInterface|null
     */
    private $fixer;

    /**
     * @var \YamlStandards\Model\Config\StandardParametersData
     */
    private $standardParametersData;

    /**
     * @param \YamlStandards\Model\CheckerInterface $checker
     * @param \YamlStandards\Model\FixerInterface|null $fixer
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     */
    public function __construct(CheckerInterface $checker, ?FixerInterface $fixer, StandardParametersData $standardParametersData)
    {
        $this->checker = $checker;
        $this->fixer = $fixer;
        $this->standardParametersData = $standardParametersData;
    }

    /**
     * @return \YamlStandards\Model\CheckerInterface
     */
    public function getChecker(): CheckerInterface
    {
        return $this->checker;
    }

    /**
     * @return \YamlStandards\Model\FixerInterface|null
     */
    public function getFixer(): ?FixerInterface
    {
        return $this->fixer;
    }

    /**
     * @return \YamlStandards\Model\Config\StandardParametersData
     */
    public function getStandardParametersData(): StandardParametersData
    {
        return $this->standardParametersData;
    }
}
