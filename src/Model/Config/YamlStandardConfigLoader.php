<?php

declare(strict_types=1);

namespace YamlStandards\Model\Config;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Parser;
use YamlStandards\Command\Service\YamlFilesPathService;
use YamlStandards\Model\Config\Exception\YamlStandardConfigNotFoundException;

class YamlStandardConfigLoader
{
    /**
     * @param string $configFilename
     * @return \YamlStandards\Model\Config\YamlStandardConfigTotalData
     */
    public function loadFromYaml(string $configFilename): YamlStandardConfigTotalData
    {
        if (file_exists($configFilename) === false) {
            throw new YamlStandardConfigNotFoundException('Config file ' . $configFilename . ' does not exist');
        }

        $yamlParser = new Parser();
        $yamlStandardsConfigDefinition = new YamlStandardConfigDefinition();
        $processor = new Processor();

        $configContent = $yamlParser->parseFile($configFilename);
        $outputConfigs = $processor->processConfiguration($yamlStandardsConfigDefinition, [$configContent]);

        return $this->createConfigData($outputConfigs);
    }

    /**
     * @param array $outputConfigs
     * @return \YamlStandards\Model\Config\YamlStandardConfigTotalData
     */
    private function createConfigData(array $outputConfigs): YamlStandardConfigTotalData
    {
        $yamlStandardConfigsSingleData = [];

        foreach ($outputConfigs as $outputConfig) {
            $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles($outputConfig[YamlStandardConfigDefinition::CONFIG_PATHS_TO_CHECK]);
            $pathToExcludedYamlFiles = YamlFilesPathService::getPathToYamlFiles($outputConfig[YamlStandardConfigDefinition::CONFIG_EXCLUDED_PATHS]);
            $standardConfigs = $this->createStandardsConfig($outputConfig[YamlStandardConfigDefinition::CONFIG_CHECKERS]);

            $yamlStandardConfigsSingleData[] = new YamlStandardConfigSingleData($pathToYamlFiles, $pathToExcludedYamlFiles, $standardConfigs);
        }

        return new YamlStandardConfigTotalData($yamlStandardConfigsSingleData);
    }

    /**
     * @param array $checkersConfig
     * @return \YamlStandards\Model\Config\YamlStandardConfigSingleStandardData[]
     */
    private function createStandardsConfig(array $checkersConfig): array
    {
        $yamlStandardConfigsSingleCheckerData = [];

        foreach ($checkersConfig as $checkerConfig) {
            $checkerClassName = $checkerConfig[YamlStandardConfigDefinition::CONFIG_PATH_TO_CHECKER];
            $checkerClass = new $checkerClassName();

            $fixerClassName = str_replace('Checker', 'Fixer', $checkerClassName);
            $fixerClass = class_exists($fixerClassName) === false ? null : new $fixerClassName();

            $parameters = $checkerConfig[YamlStandardConfigDefinition::CONFIG_PARAMETERS_FOR_CHECKER];
            $depth = $parameters[YamlStandardConfigDefinition::CONFIG_PARAMETERS_DEPTH];
            $indents = $parameters[YamlStandardConfigDefinition::CONFIG_PARAMETERS_INDENTS];
            $level = $parameters[YamlStandardConfigDefinition::CONFIG_PARAMETERS_LEVEL];
            $parametersClass = new StandardParametersData($depth, $indents, $level);

            $yamlStandardConfigsSingleCheckerData[] = new YamlStandardConfigSingleStandardData($checkerClass, $fixerClass, $parametersClass);
        }

        return $yamlStandardConfigsSingleCheckerData;
    }
}
