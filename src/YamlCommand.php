<?php

namespace YamlAlphabeticalChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use YamlAlphabeticalChecker\Checker\YamlAlphabeticalChecker;
use YamlAlphabeticalChecker\Checker\YamlIndentChecker;
use YamlAlphabeticalChecker\Checker\YamlInlineChecker;

class YamlCommand extends Command
{
    const
        ARGUMENT_DIRS_OR_FILES = 'dirsOrFiles',
        OPTION_EXCLUDE = 'exclude',
        OPTION_CHECK_YAML_COUNT_OF_INDENTS = 'check-indents-count-of-indents',
        OPTION_CHECK_INLINE = 'check-inline';

    protected static $defaultName = 'yaml-alphabetical-check';

    protected function configure()
    {
        $this
            ->setDescription('Check if yaml files is alphabetically sorted')
            ->addArgument(self::ARGUMENT_DIRS_OR_FILES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to directories or files to check')
            ->addOption(self::OPTION_EXCLUDE, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude file mask from check')
            ->addOption(self::OPTION_CHECK_YAML_COUNT_OF_INDENTS, null, InputOption::VALUE_REQUIRED, 'Check count of indents in yaml file')
            ->addOption(self::OPTION_CHECK_INLINE, null, InputOption::VALUE_NONE, 'Check yaml file complies inline standards');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Reporting::startTiming();

        $dirsOrFiles = $input->getArgument(self::ARGUMENT_DIRS_OR_FILES);
        $excludedFileMasks = $input->getOption(self::OPTION_EXCLUDE);
        $countOfIndents = $input->getOption(self::OPTION_CHECK_YAML_COUNT_OF_INDENTS);
        $checkInlineStandard = $input->getOption(self::OPTION_CHECK_INLINE);

        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles($dirsOrFiles);
        $processOutput = new ProcessOutput(count($pathToYamlFiles));

        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $yamlIndentChecker = new YamlIndentChecker();
        $yamlInlineChecker = new YamlInlineChecker();
        $results = [];

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            if ($this->isFileSkipped($pathToYamlFile, $excludedFileMasks)) {
                $output->write($processOutput->process(ProcessOutput::STATUS_CODE_SKIPP));
                continue;
            }

            if (is_readable($pathToYamlFile) === false) {
                $message = 'File is not readable.';
                $results[] = new Result($pathToYamlFile, $message, Result::RESULT_CODE_GENERAL_ERROR);
                $output->write($processOutput->process(ProcessOutput::STATUS_CODE_ERROR));
                continue;
            }

            try {
                $sortCheckResult = $yamlAlphabeticalChecker->isDataSorted($pathToYamlFile);

                if ($countOfIndents !== null) {
                    $indentCheckResult = $yamlIndentChecker->getCorrectIndentsInFile($pathToYamlFile, $countOfIndents);

                    if ($indentCheckResult === null) {
                        $output->write($processOutput->process(ProcessOutput::STATUS_CODE_OK));
                    } else {
                        $results[] = new Result($pathToYamlFile, $indentCheckResult, Result::RESULT_CODE_INVALID_SORT);
                        $output->write($processOutput->process(ProcessOutput::STATUS_CODE_INVALID_SORT));
                    }
                }

                if ($checkInlineStandard === true) {
                    $inlineCheckResult = $yamlInlineChecker->getRightCompilesData($pathToYamlFile);

                    if ($inlineCheckResult === null) {
                        $output->write($processOutput->process(ProcessOutput::STATUS_CODE_OK));
                    } else {
                        $results[] = new Result($pathToYamlFile, $inlineCheckResult, Result::RESULT_CODE_INVALID_SORT);
                        $output->write($processOutput->process(ProcessOutput::STATUS_CODE_INVALID_SORT));
                    }
                }

                if ($sortCheckResult) {
                    $output->write($processOutput->process(ProcessOutput::STATUS_CODE_OK));
                } else {
                    $diff = $yamlAlphabeticalChecker->getDifference($pathToYamlFile);
                    $results[] = new Result($pathToYamlFile, $diff, Result::RESULT_CODE_INVALID_SORT);
                    $output->write($processOutput->process(ProcessOutput::STATUS_CODE_INVALID_SORT));
                }
            } catch (ParseException $e) {
                $message = sprintf('Unable to parse the YAML string: %s', $e->getMessage());
                $results[] = new Result($pathToYamlFile, $message, Result::RESULT_CODE_GENERAL_ERROR);
                $output->write($processOutput->process(ProcessOutput::STATUS_CODE_ERROR));
            }
        }
        $output->writeln($processOutput->getLegend());

        return $this->printOutput($output, $results);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \YamlAlphabeticalChecker\Result[] $results
     * @return int
     */
    private function printOutput(OutputInterface $output, array $results)
    {
        $resultCode = 0;

        foreach ($results as $result) {
            $resultCode = $result->getResultCode() > $resultCode ? $result->getResultCode() : $resultCode;
            $output->writeln(sprintf('FILE: %s', $result->getPathToFile()));
            $output->writeln('-------------------------------------------------');
            $output->writeln($result->getMessage() . PHP_EOL);
        }

        $output->writeln(Reporting::printRunTime());

        return $resultCode;
    }

    /**
     * @param string $pathToFile
     * @param array $excludedFileMasks
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
