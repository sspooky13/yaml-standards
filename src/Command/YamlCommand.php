<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Exception\ParseException;
use YamlStandards\Command\Service\ResultService;
use YamlStandards\Model\Config\YamlStandardConfigLoader;
use YamlStandards\Result\Result;

class YamlCommand extends Command
{
    private const COMMAND_NAME = 'yaml-standards';

    public const ARGUMENT_PATH_TO_CONFIG_FILE = 'pathToConfigFile';
    public const OPTION_FIX = 'fix';

    protected static $defaultName = self::COMMAND_NAME;

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME) // set command name for symfony/console lower version as 3.4
            ->setDescription('Check yaml files respect standards')
            ->addArgument(self::ARGUMENT_PATH_TO_CONFIG_FILE, InputArgument::OPTIONAL, 'Path to configuration file. By default configuration file is looking in root directory', './yaml-standards.yaml')
            ->addOption(self::OPTION_FIX, null, InputOption::VALUE_NONE, 'Automatically fix problems');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputSettingData = new InputSettingData($input);

        $yamlStandardConfigLoader = new YamlStandardConfigLoader();
        $yamlStandardConfigTotalData = $yamlStandardConfigLoader->loadFromYaml($inputSettingData->getPathToConfigFile());

        $symfonyStyle = new SymfonyStyle($input, $output);
        $progressBar = $symfonyStyle->createProgressBar($yamlStandardConfigTotalData->getTotalCountOfYamlFiles());
        $progressBar->setFormat('debug');
        $results = [[]];

        foreach ($yamlStandardConfigTotalData->getYamlStandardConfigsSingleData() as $yamlStandardConfigSingleData) {
            foreach ($yamlStandardConfigSingleData->getPathToYamlFiles() as $pathToYamlFile) {
                $fileResults = [];
                if ($this->isFileExcluded($pathToYamlFile, $yamlStandardConfigSingleData->getPathToExcludedYamlFiles())) {
                    $progressBar->advance();
                    continue;
                }

                if (is_readable($pathToYamlFile) === false) {
                    $message = 'File is not readable.';
                    $fileResults[] = new Result($pathToYamlFile, Result::RESULT_CODE_GENERAL_ERROR, $message);
                    $progressBar->advance();
                    continue;
                }

                try {
                    foreach ($yamlStandardConfigSingleData->getYamlStandardConfigsSingleStandardData() as $yamlStandardConfigSingleCheckerData) {
                        $standardParametersData = $yamlStandardConfigSingleCheckerData->getStandardParametersData();
                        $fixer = $yamlStandardConfigSingleCheckerData->getFixer();
                        if ($fixer !== null && $inputSettingData->isFixEnabled()) {
                            $fileResults[] = $fixer->runFix($pathToYamlFile, $pathToYamlFile, $standardParametersData);
                        } else {
                            $fileResults[] = $yamlStandardConfigSingleCheckerData->getChecker()->runCheck($pathToYamlFile, $standardParametersData);
                        }
                    }
                } catch (ParseException $e) {
                    $message = sprintf('Unable to parse the YAML string: %s', $e->getMessage());
                    $fileResults[] = new Result($pathToYamlFile, Result::RESULT_CODE_GENERAL_ERROR, $message);
                }

                $results[] = $fileResults;
                $progressBar->advance();
            }
        }
        $progressBar->finish();
        /** @var \YamlStandards\Result\Result[] $mergedResult */
        $mergedResult = array_merge(...$results); // add all results to one array instead of multidimensional array with results for every file

        return $this->printOutput($output, $mergedResult);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \YamlStandards\Result\Result[] $results
     * @return int
     */
    private function printOutput(OutputInterface $output, array $results): int
    {
        $output->writeln(PHP_EOL);
        foreach ($results as $result) {
            if ($result->getResultCode() !== Result::RESULT_CODE_OK) {
                $output->writeln(sprintf('FILE: %s', $result->getPathToFile()));
                $output->writeln('-------------------------------------------------');
                $output->writeln($result->getMessage() . PHP_EOL);

                if ($result->canBeFixedByFixer()) {
                    $output->writeln('<fg=red>This can be fixed by `--fix` option</fg=red>' . PHP_EOL);
                }
            }
        }

        return ResultService::getResultCodeByResults($results);
    }

    /**
     * @param string $pathToYamlFile
     * @param string[] $pathToExcludedYamlFiles
     * @return bool
     */
    private function isFileExcluded(string $pathToYamlFile, array $pathToExcludedYamlFiles): bool
    {
        if (in_array($pathToYamlFile, $pathToExcludedYamlFiles, true)) {
            return true;
        }

        return false;
    }
}
