<?php

namespace YamlStandards\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\ProcessOutput;
use YamlStandards\Reporting;
use YamlStandards\Result;
use YamlStandards\Service\ProcessOutputService;
use YamlStandards\Service\ResultService;
use YamlStandards\Service\StandardClassesLoaderService;
use YamlStandards\Service\YamlFilesPathService;

class YamlCommand extends Command
{
    const COMMAND_NAME = 'yaml-standards';

    const
        ARGUMENT_DIRS_OR_FILES = 'dirsOrFiles',
        OPTION_EXCLUDE_BY_NAME = 'exclude-by-name',
        OPTION_EXCLUDE_DIR = 'exclude-dir',
        OPTION_EXCLUDE_FILE = 'exclude-file',
        OPTION_CHECK_ALPHABETICAL_SORT_DEPTH = 'check-alphabetical-sort-depth',
        OPTION_CHECK_YAML_COUNT_OF_INDENTS = 'check-indents-count-of-indents',
        OPTION_CHECK_INLINE = 'check-inline',
        OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS = 'check-spaces-between-groups-to-level';

    protected static $defaultName = self::COMMAND_NAME;

    protected function configure()
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
            ->addOption(self::OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS, null, InputOption::VALUE_REQUIRED, 'Check yaml file have correct space between groups for set level');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Reporting::startTiming();

        $inputSettingData = new InputSettingData($input);

        $pathToYamlFilesWithSkippedFiles = YamlFilesPathService::getPathToYamlFiles($inputSettingData, true);
        $pathToYamlFilesWithoutSkippedFiles = YamlFilesPathService::getPathToYamlFiles($inputSettingData);
        $processOutput = new ProcessOutput(count($pathToYamlFilesWithSkippedFiles));

        $checkerInterfaces = StandardClassesLoaderService::getCheckerClassesByInputSettingData($inputSettingData);
        $results = [[]];

        foreach ($pathToYamlFilesWithSkippedFiles as $pathToYamlFile) {
            $fileResults = [];
            if ($this->isFileSkipped($pathToYamlFile, $pathToYamlFilesWithoutSkippedFiles, $inputSettingData->getExcludedFileMasks())) {
                $output->write($processOutput->process(ProcessOutput::STATUS_CODE_SKIPP));
                continue;
            }

            if (is_readable($pathToYamlFile) === false) {
                $message = 'File is not readable.';
                $fileResults[] = new Result($pathToYamlFile, Result::RESULT_CODE_GENERAL_ERROR, $message);
                $output->write($processOutput->process(ProcessOutput::STATUS_CODE_ERROR));
                continue;
            }

            try {
                // check yaml is valid
                Yaml::parse(file_get_contents($pathToYamlFile), Yaml::PARSE_CUSTOM_TAGS);

                foreach ($checkerInterfaces as $checkerInterface) {
                    $fileResults[] = $checkerInterface->check($pathToYamlFile, $inputSettingData);
                }
            } catch (ParseException $e) {
                $message = sprintf('Unable to parse the YAML string: %s', $e->getMessage());
                $fileResults[] = new Result($pathToYamlFile, Result::RESULT_CODE_GENERAL_ERROR, $message);
            }

            $results[] = $fileResults;
            $output->write($processOutput->process(ProcessOutputService::getWorstStatusCodeByResults($fileResults)));
        }
        $output->writeln($processOutput->getLegend());
        $results = array_merge(...$results); // add all results to one array instead of multidimensional array with results for every file

        return $this->printOutput($output, $results);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \YamlStandards\Result[] $results
     * @return int
     */
    private function printOutput(OutputInterface $output, array $results)
    {
        foreach ($results as $result) {
            if ($result->getResultCode() !== Result::RESULT_CODE_OK) {
                $output->writeln(sprintf('FILE: %s', $result->getPathToFile()));
                $output->writeln('-------------------------------------------------');
                $output->writeln($result->getMessage() . PHP_EOL);
            }
        }

        $output->writeln(Reporting::printRunTime());

        return ResultService::getResultCodeByResults($results);
    }

    /**
     * @param string $pathToFile
     * @param string[] $pathToYamlFilesWithoutSkippedFiles
     * @param string[] $excludedFileMasks
     * @return bool
     */
    private function isFileSkipped($pathToFile, $pathToYamlFilesWithoutSkippedFiles, array $excludedFileMasks = [])
    {
        if (in_array($pathToFile, $pathToYamlFilesWithoutSkippedFiles, true) === false) {
            return true;
        }

        foreach ($excludedFileMasks as $excludedFileMask) {
            if (strpos($pathToFile, $excludedFileMask) !== false) {
                return true;
            }
        }

        return false;
    }
}