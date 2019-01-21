<?php

namespace YamlStandards;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use YamlStandards\Checker\YamlAlphabeticalChecker;
use YamlStandards\Checker\YamlIndentChecker;
use YamlStandards\Checker\YamlInlineChecker;
use YamlStandards\Checker\YamlSpacesBetweenGroupsChecker;
use YamlStandards\Service\ProcessOutputService;
use YamlStandards\Service\ResultService;
use YamlStandards\Service\YamlFilesPathService;

class YamlCommand extends Command
{
    const
        ARGUMENT_DIRS_OR_FILES = 'dirsOrFiles',
        OPTION_EXCLUDE_BY_NAME = 'exclude-by-name',
        OPTION_EXCLUDE_DIR = 'exclude-dir',
        OPTION_CHECK_ALPHABETICAL_SORT_DEPTH = 'check-alphabetical-sort-depth',
        OPTION_CHECK_YAML_COUNT_OF_INDENTS = 'check-indents-count-of-indents',
        OPTION_CHECK_INLINE = 'check-inline',
        OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS = 'check-spaces-between-groups-to-level';

    protected static $defaultName = 'yaml-standards';

    protected function configure()
    {
        $this
            ->setDescription('Check if yaml files is alphabetically sorted')
            ->addArgument(self::ARGUMENT_DIRS_OR_FILES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to directories or files to check')
            ->addOption(self::OPTION_EXCLUDE_BY_NAME, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude file mask from check')
            ->addOption(self::OPTION_EXCLUDE_DIR, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude dirs from check')
            ->addOption(self::OPTION_CHECK_ALPHABETICAL_SORT_DEPTH, null, InputOption::VALUE_REQUIRED, 'Check yaml file is right sorted in set depth')
            ->addOption(self::OPTION_CHECK_YAML_COUNT_OF_INDENTS, null, InputOption::VALUE_REQUIRED, 'Check count of indents in yaml file')
            ->addOption(self::OPTION_CHECK_INLINE, null, InputOption::VALUE_NONE, 'Check yaml file complies inline standards')
            ->addOption(self::OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS, null, InputOption::VALUE_REQUIRED, 'Check yaml file have correct space between groups for set level');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     *
     * @SuppressWarnings("ExcessiveMethodLength")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Reporting::startTiming();

        $dirsOrFiles = $input->getArgument(self::ARGUMENT_DIRS_OR_FILES);
        $excludedFileMasks = $input->getOption(self::OPTION_EXCLUDE_BY_NAME);
        $excludedDirs = $input->getOption(self::OPTION_EXCLUDE_DIR);
        $checkAlphabeticalSortDepth = $input->getOption(self::OPTION_CHECK_ALPHABETICAL_SORT_DEPTH);
        $countOfIndents = $input->getOption(self::OPTION_CHECK_YAML_COUNT_OF_INDENTS);
        $checkInlineStandard = $input->getOption(self::OPTION_CHECK_INLINE);
        $levelForCheckSpacesBetweenGroups = $input->getOption(self::OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS);

        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles($dirsOrFiles, $excludedDirs);
        $processOutput = new ProcessOutput(count($pathToYamlFiles));

        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $yamlIndentChecker = new YamlIndentChecker();
        $yamlInlineChecker = new YamlInlineChecker();
        $yamlSpacesBetweenGroupsChecker = new YamlSpacesBetweenGroupsChecker();
        $results = [[]];

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            $fileResults = [];
            if ($this->isFileSkipped($pathToYamlFile, $excludedFileMasks)) {
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

                if ($checkAlphabeticalSortDepth !== null) {
                    $fileResults[] = $yamlAlphabeticalChecker->getRightSortedData($pathToYamlFile, $checkAlphabeticalSortDepth);
                }

                if ($countOfIndents !== null) {
                    $fileResults[] = $yamlIndentChecker->getCorrectIndentsInFile($pathToYamlFile, $countOfIndents);
                }

                if ($checkInlineStandard === true) {
                    $fileResults[] = $yamlInlineChecker->getRightCompilesData($pathToYamlFile);
                }

                if ($levelForCheckSpacesBetweenGroups !== null) {
                    $fileResults[] = $yamlSpacesBetweenGroupsChecker->getCorrectDataWithSpacesBetweenGroups($pathToYamlFile, $levelForCheckSpacesBetweenGroups);
                }
            } catch (ParseException $e) {
                $message = sprintf('Unable to parse the YAML string: %s', $e->getMessage());
                $fileResults[] = new Result($pathToYamlFile, Result::RESULT_CODE_GENERAL_ERROR, $message);
            }

            $results[] = $fileResults;
            $output->write($processOutput->process(ProcessOutputService::getWorstStatusCodeByResults($fileResults)));
        }
        $output->writeln($processOutput->getLegend());
        $results = array_merge(...$results); // show all file results

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
     * @param string[] $excludedFileMasks
     * @return bool
     */
    private function isFileSkipped($pathToFile, array $excludedFileMasks = [])
    {
        foreach ($excludedFileMasks as $excludedFileMask) {
            if (strpos($pathToFile, $excludedFileMask) !== false) {
                return true;
            }
        }

        return false;
    }
}
