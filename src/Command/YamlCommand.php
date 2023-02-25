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
use YamlStandards\Model\Component\Cache\NativeCache;
use YamlStandards\Model\Component\Cache\NoCache;
use YamlStandards\Model\Config\YamlStandardConfigLoader;
use YamlStandards\Result\Result;

class YamlCommand extends Command
{
    private const COMMAND_NAME = 'yaml-standards';

    public const ARGUMENT_PATH_TO_CONFIG_FILE = 'pathToConfigFile';
    public const OPTION_FIX = 'fix';
    public const OPTION_PATH_TO_CACHE_DIR = 'path-to-cache-dir';
    public const OPTION_DISABLE_CACHE = 'no-cache';

    protected static $defaultName = self::COMMAND_NAME;

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME) // set command name for symfony/console lower version as 3.4
            ->setDescription('Check yaml files respect standards')
            ->addArgument(self::ARGUMENT_PATH_TO_CONFIG_FILE, InputArgument::OPTIONAL, 'Path to configuration file. By default configuration file is looking in root directory', './yaml-standards.yaml')
            ->addOption(self::OPTION_FIX, null, InputOption::VALUE_NONE, 'Automatically fix problems')
            ->addOption(self::OPTION_PATH_TO_CACHE_DIR, null, InputOption::VALUE_REQUIRED, 'Custom path to cache dir', '/')
            ->addOption(self::OPTION_DISABLE_CACHE, null, InputOption::VALUE_NONE, 'Disable cache functionality');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputSettingData = new InputSettingData($input);
        $yamlStandardConfigLoader = new YamlStandardConfigLoader();
        $pathToConfigFile = $inputSettingData->getPathToConfigFile();
        $pathToCacheDir = $inputSettingData->getPathToCacheDir();
        $yamlStandardConfigTotalData = $yamlStandardConfigLoader->loadFromYaml($pathToConfigFile);
        $cache = $inputSettingData->isCacheDisabled() ? new NoCache() : new NativeCache();
        $cache->deleteCacheFileIfConfigFileWasChanged($pathToConfigFile, $pathToCacheDir);

        $symfonyStyle = new SymfonyStyle($input, $output);
        $progressBar = $symfonyStyle->createProgressBar($yamlStandardConfigTotalData->getTotalCountOfFiles());
        $progressBar->setFormat('debug');
        $results = [[]];

        foreach ($yamlStandardConfigTotalData->getYamlStandardConfigsSingleData() as $configNumber => $yamlStandardConfigSingleData) {
            // config number 0 is reserved for config file
            ++$configNumber;

            $filesToCache = [];
            $cachedPathToFiles = $cache->getCachedPathToFiles($yamlStandardConfigSingleData->getPathToFiles(), $configNumber, $pathToCacheDir);
            foreach ($cachedPathToFiles as $pathToFile) {
                $fileResults = [];
                if ($this->isFileExcluded($pathToFile, $yamlStandardConfigSingleData->getPathToExcludedFiles())) {
                    $filesToCache[] = $pathToFile;
                    $progressBar->advance();
                    continue;
                }

                if (is_readable($pathToFile) === false) {
                    $message = 'File is not readable.';
                    $results[] = [new Result($pathToFile, Result::RESULT_CODE_GENERAL_ERROR, $message)];
                    $progressBar->advance();
                    continue;
                }

                try {
                    foreach ($yamlStandardConfigSingleData->getYamlStandardConfigsSingleStandardData() as $yamlStandardConfigSingleCheckerData) {
                        $standardParametersData = $yamlStandardConfigSingleCheckerData->getStandardParametersData();
                        $checker = $yamlStandardConfigSingleCheckerData->getChecker();
                        $fixer = $yamlStandardConfigSingleCheckerData->getFixer();

                        if ($fixer !== null && $inputSettingData->isFixEnabled()) {
                            $result = $fixer->runFix($pathToFile, $pathToFile, $standardParametersData);
                        } else {
                            $result = $checker->runCheck($pathToFile, $standardParametersData);
                        }
                        $fileResults[] = $result;
                    }
                } catch (ParseException $e) {
                    $message = sprintf('Unable to parse the YAML string: %s', $e->getMessage());
                    $fileResults[] = new Result($pathToFile, Result::RESULT_CODE_GENERAL_ERROR, $message);
                }

                if (ResultService::getResultCodeByResults($fileResults) === Result::RESULT_CODE_OK_AS_INTEGER) {
                    $filesToCache[] = $pathToFile;
                }
                $results[] = $fileResults;
                $progressBar->advance();
            }

            $cache->cacheFiles($filesToCache, $configNumber, $pathToCacheDir);
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
     * @param string $pathToFile
     * @param string[] $pathToExcludedFiles
     * @return bool
     */
    private function isFileExcluded(string $pathToFile, array $pathToExcludedFiles): bool
    {
        if (in_array($pathToFile, $pathToExcludedFiles, true)) {
            return true;
        }

        return false;
    }
}
