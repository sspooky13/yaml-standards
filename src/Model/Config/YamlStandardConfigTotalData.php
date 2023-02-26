<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

class YamlStandardConfigTotalData
{
    /**
     * @var \YamlStandards\Model\Config\YamlStandardConfigSingleData[]
     */
    private $yamlStandardConfigsSingleData;

    /**
     * @param \YamlStandards\Model\Config\YamlStandardConfigSingleData[] $yamlStandardConfigsSingleData
     */
    public function __construct(array $yamlStandardConfigsSingleData)
    {
        $this->yamlStandardConfigsSingleData = $yamlStandardConfigsSingleData;
    }

    /**
     * @return \YamlStandards\Model\Config\YamlStandardConfigSingleData[]
     */
    public function getYamlStandardConfigsSingleData(): array
    {
        return $this->yamlStandardConfigsSingleData;
    }

    /**
     * @return int
     */
    public function getTotalCountOfFiles(): int
    {
        $totalFiles = 0;

        foreach ($this->getYamlStandardConfigsSingleData() as $yamlStandardConfigSingleData) {
            $totalFiles += count($yamlStandardConfigSingleData->getPathToFiles());
        }

        return $totalFiles;
    }
}
