<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use YamlStandards\Command\Service\ProcessOutputService;
use YamlStandards\Command\Service\ResultService;
use YamlStandards\Command\Service\StandardClassesLoaderService;
use YamlStandards\Model\Component\YamlService;
use YamlStandards\Model\Config\YamlStandardConfigLoader;
use YamlStandards\Result\Result;

class YamlCommand extends Command
{
    private const COMMAND_NAME = 'yaml-standards';

    public const
        ARGUMENT_DIRS_OR_FILES = 'dirsOrFiles',
        OPTION_EXCLUDE_BY_NAME = 'exclude-by-name',
        OPTION_EXCLUDE_DIR = 'exclude-dir',
        OPTION_EXCLUDE_FILE = 'exclude-file',
        OPTION_CHECK_ALPHABETICAL_SORT_DEPTH = 'check-alphabetical-sort-depth',
        OPTION_CHECK_YAML_COUNT_OF_INDENTS = 'check-indents-count-of-indents',
        OPTION_CHECK_INLINE = 'check-inline',
        OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS = 'check-spaces-between-groups-to-level',
        OPTION_FIX = 'fix';

    protected static $defaultName = self::COMMAND_NAME;

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME) // set command name for symfony/console lower version as 3.4
            ->setDescription('Check yaml files respect standards')
            ->addArgument(self::ARGUMENT_DIRS_OR_FILES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to directories or files to check')
            ->addOption(self::OPTION_EXCLUDE_BY_NAME, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude file mask from check')
            ->addOption(self::OPTION_EXCLUDE_DIR, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude path to dirs from check')
            ->addOption(self::OPTION_EXCLUDE_FILE, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude path to files from check')
            ->addOption(self::OPTION_CHECK_ALPHABETICAL_SORT_DEPTH, null, InputOption::VALUE_REQUIRED, 'Check yaml file is right sorted in set depth')
            ->addOption(self::OPTION_CHECK_YAML_COUNT_OF_INDENTS, null, InputOption::VALUE_REQUIRED, 'Check count of indents in yaml file')
            ->addOption(self::OPTION_CHECK_INLINE, null, InputOption::VALUE_NONE, 'Check yaml file complies inline standards')
            ->addOption(self::OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS, null, InputOption::VALUE_REQUIRED, 'Check yaml file have correct space between groups for set level')
            ->addOption(self::OPTION_FIX, null, InputOption::VALUE_NONE, 'Automatically fix problems');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        Reporting::startTiming();

        $inputSettingData = new InputSettingData($input);
        $yamlStandardConfigLoader = new YamlStandardConfigLoader();
        $yamlStandardConfigTotalData = $yamlStandardConfigLoader->loadFromYaml('./example/yaml-standards.yaml');

        $processOutput = new ProcessOutput($yamlStandardConfigTotalData->getTotalCountOfYamlFiles());

        $fixerInterfaces = StandardClassesLoaderService::getFixerClassesByInputSettingData($inputSettingData);
        $checkerInterfaces = StandardClassesLoaderService::getCheckerClassesByInputSettingData($inputSettingData);
        $results = [[]];

        foreach ($yamlStandardConfigTotalData->getYamlStandardConfigsSingleData() as $yamlStandardConfigSingleData) {
            foreach ($yamlStandardConfigSingleData->getPathToYamlFiles() as $pathToYamlFile) {
                $fileResults = [];
                if ($this->isFileExcluded($pathToYamlFile, $yamlStandardConfigSingleData->getPathToExcludedYamlFiles())) {
                    $output->write($processOutput->process(ProcessOutput::STATUS_CODE_SKIPP));
                    continue;
                }

                if (is_readable($pathToYamlFile) === false) {
                    $message = 'File is not readable.';
                    $fileResults[] = new Result($pathToYamlFile, Result::RESULT_CODE_GENERAL_ERROR, ProcessOutput::STATUS_CODE_ERROR, $message);
                    $output->write($processOutput->process(ProcessOutput::STATUS_CODE_ERROR));
                    continue;
                }

                try {
                    // check yaml is valid
                    YamlService::getYamlData($pathToYamlFile);

                    foreach ($yamlStandardConfigSingleData->getYamlStandardConfigsSingleStandardData() as $yamlStandardConfigSingleCheckerData) {
                        $standardParametersData = $yamlStandardConfigSingleCheckerData->getStandardParametersData();
                        $fixer = $yamlStandardConfigSingleCheckerData->getFixer();
                        if ($fixer !== null && $inputSettingData->isFixEnabled()) {
                            $fileResults[] = $fixer->fix($pathToYamlFile, $pathToYamlFile, $standardParametersData);
                        } else {
                            $fileResults[] = $yamlStandardConfigSingleCheckerData->getChecker()->check($pathToYamlFile, $standardParametersData);
                        }
                    }
                } catch (ParseException $e) {
                    $message = sprintf('Unable to parse the YAML string: %s', $e->getMessage());
                    $fileResults[] = new Result($pathToYamlFile, Result::RESULT_CODE_GENERAL_ERROR, ProcessOutput::STATUS_CODE_ERROR, $message);
                }

                $results[] = $fileResults;
                $output->write($processOutput->process(ProcessOutputService::getWorstStatusCodeByResults($fileResults)));
            }
        }
        $output->writeln($processOutput->getLegend());
        $results = array_merge(...$results); // add all results to one array instead of multidimensional array with results for every file

        return $this->printOutput($output, $results);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \YamlStandards\Result\Result[] $results
     * @return int
     */
    private function printOutput(OutputInterface $output, array $results): int
    {
        foreach ($results as $result) {
            if ($result->getStatusCode() !== ProcessOutput::STATUS_CODE_OK) {
                $output->writeln(sprintf('FILE: %s', $result->getPathToFile()));
                $output->writeln('-------------------------------------------------');
                $output->writeln($result->getMessage() . PHP_EOL);

                if ($result->canBeFixedByFixer()) {
                    $output->writeln('<fg=red>This can be fixed by `--fix` option</fg=red>' . PHP_EOL);
                }
            }
        }

        $output->writeln(Reporting::printRunTime());

        return ResultService::getResultCodeByResults($results);
    }

    /**
     * @param string $pathToYamlFile
     * @param string[] $pathToExcludedYamlFiles
     * @return bool
     */
    private function isFileExcluded($pathToYamlFile, array $pathToExcludedYamlFiles): bool
    {
        if (in_array($pathToYamlFile, $pathToExcludedYamlFiles, true)) {
            return true;
        }

        return false;
    }
}
