<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

class YamlStandardConfigSingleData
{
    /**
     * @var string[]
     */
    private $pathToFiles;

    /**
     * @var string[]
     */
    private $pathToExcludedFiles;

    /**
     * @var \YamlStandards\Model\Config\YamlStandardConfigSingleStandardData[]
     */
    private $yamlStandardConfigsSingleStandardData;

    /**
     * @param string[] $pathToFiles
     * @param string[] $pathToExcludedFiles
     * @param \YamlStandards\Model\Config\YamlStandardConfigSingleStandardData[] $yamlStandardConfigsSingleStandardData
     */
    public function __construct(array $pathToFiles, array $pathToExcludedFiles, array $yamlStandardConfigsSingleStandardData)
    {
        $this->pathToFiles = $pathToFiles;
        $this->pathToExcludedFiles = $pathToExcludedFiles;
        $this->yamlStandardConfigsSingleStandardData = $yamlStandardConfigsSingleStandardData;
    }

    /**
     * @return string[]
     */
    public function getPathToFiles(): array
    {
        return $this->pathToFiles;
    }

    /**
     * @return string[]
     */
    public function getPathToExcludedFiles(): array
    {
        return $this->pathToExcludedFiles;
    }

    /**
     * @return \YamlStandards\Model\Config\YamlStandardConfigSingleStandardData[]
     */
    public function getYamlStandardConfigsSingleStandardData(): array
    {
        return $this->yamlStandardConfigsSingleStandardData;
    }
}
