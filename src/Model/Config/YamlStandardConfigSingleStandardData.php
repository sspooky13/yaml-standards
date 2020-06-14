<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

use YamlStandards\Model\AbstractChecker;
use YamlStandards\Model\AbstractFixer;

class YamlStandardConfigSingleStandardData
{
    /**
     * @var \YamlStandards\Model\AbstractChecker
     */
    private $checker;

    /**
     * @var \YamlStandards\Model\AbstractFixer|null
     */
    private $fixer;

    /**
     * @var \YamlStandards\Model\Config\StandardParametersData
     */
    private $standardParametersData;

    /**
     * @param \YamlStandards\Model\AbstractChecker $checker
     * @param \YamlStandards\Model\AbstractFixer|null $fixer
     * @param \YamlStandards\Model\Config\StandardParametersData $standardParametersData
     */
    public function __construct(AbstractChecker $checker, ?AbstractFixer $fixer, StandardParametersData $standardParametersData)
    {
        $this->checker = $checker;
        $this->fixer = $fixer;
        $this->standardParametersData = $standardParametersData;
    }

    /**
     * @return \YamlStandards\Model\AbstractChecker
     */
    public function getChecker(): AbstractChecker
    {
        return $this->checker;
    }

    /**
     * @return \YamlStandards\Model\AbstractFixer|null
     */
    public function getFixer(): ?AbstractFixer
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
