<?php

namespace YamlAlphabeticalChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlCheckCommand extends Command
{
    const
        ARGUMENT_DIRS_OR_FILES = 'dirsOrFiles',
        OPTION_EXCLUDE = 'exclude';

    protected function configure()
    {
        $this
            ->setName('yaml-alphabetical-check')
            ->setDescription('Check if yaml files is alphabetically sorted')
            ->addArgument(self::ARGUMENT_DIRS_OR_FILES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to directories or files to check')
            ->addOption(self::OPTION_EXCLUDE, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude file mask from check');
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
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles($dirsOrFiles);
        $processOutput = new ProcessOutput(count($pathToYamlFiles));

        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $results = [];

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            if ($this->isFileSkipped($pathToYamlFile, $excludedFileMasks)) {
                $output->write($processOutput->process(ProcessOutput::STATUS_CODE_SKIPP));
                continue;
            }

            if (!is_readable($pathToYamlFile)) {
                $message = '<fg=red>File is not readable.</fg=red>';
                $results[] = new Result($pathToYamlFile, $message, Result::RESULT_CODE_GENERAL_ERROR);
                $output->write($processOutput->process(ProcessOutput::STATUS_CODE_ERROR));
            }

            try {
                $sortCheckResult = $yamlAlphabeticalChecker->isDataSorted($pathToYamlFile);

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
