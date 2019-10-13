<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

class YamlStandardConfigSingleData
{
    /**
     * @var string[]
     */
    private $pathToYamlFiles;

    /**
     * @var string[]
     */
    private $pathToExcludedYamlFiles;

    /**
     * @var \YamlStandards\Model\Config\YamlStandardConfigSingleStandardData[]
     */
    private $yamlStandardConfigsSingleStandardData;

    /**
     * @param string[] $pathToYamlFiles
     * @param string[] $pathToExcludedYamlFiles
     * @param \YamlStandards\Model\Config\YamlStandardConfigSingleStandardData[] $yamlStandardConfigsSingleStandardData
     */
    public function __construct(array $pathToYamlFiles, array $pathToExcludedYamlFiles, array $yamlStandardConfigsSingleStandardData)
    {
        $this->pathToYamlFiles = $pathToYamlFiles;
        $this->pathToExcludedYamlFiles = $pathToExcludedYamlFiles;
        $this->yamlStandardConfigsSingleStandardData = $yamlStandardConfigsSingleStandardData;
    }

    /**
     * @return string[]
     */
    public function getPathToYamlFiles(): array
    {
        return $this->pathToYamlFiles;
    }

    /**
     * @return string[]
     */
    public function getPathToExcludedYamlFiles(): array
    {
        return $this->pathToExcludedYamlFiles;
    }

    /**
     * @return \YamlStandards\Model\Config\YamlStandardConfigSingleStandardData[]
     */
    public function getYamlStandardConfigsSingleStandardData(): array
    {
        return $this->yamlStandardConfigsSingleStandardData;
    }
}
