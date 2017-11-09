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
        OPTION_SHOW_DIFF = 'diff',
        OPTION_EXCLUDE = 'exclude';

    protected function configure()
    {
        $this
            ->setName('yaml-alphabetical-check')
            ->setDescription('Check if yaml files is alphabetically sorted')
            ->addArgument(self::ARGUMENT_DIRS_OR_FILES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to directories or files to check')
            ->addOption(self::OPTION_SHOW_DIFF, null, InputOption::VALUE_NONE, 'Show difference in yaml file')
            ->addOption(self::OPTION_EXCLUDE, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Exclude file mask from check');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=green>Start checking yaml files.</fg=green>');
        $output->writeln('');

        $dirsOrFiles = $input->getArgument(self::ARGUMENT_DIRS_OR_FILES);
        $excludedFileMasks = $input->getOption(self::OPTION_EXCLUDE);
        $isShowDiffOptionEnabled = $input->getOption(self::OPTION_SHOW_DIFF) === true;
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles($dirsOrFiles, $excludedFileMasks);

        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $errors = [];

        foreach ($pathToYamlFiles as $pathToYamlFile) {
            $output->write(sprintf('Checking %s: ', $pathToYamlFile));

            if (!is_readable($pathToYamlFile)) {
                $output->write(sprintf('<fg=red>File is not readable.</fg=red>'));
                return 1;
            }

            try {
                $sortCheckResult = $yamlAlphabeticalChecker->isDataSorted($pathToYamlFile);

                if ($sortCheckResult) {
                    $output->writeln('<fg=green>OK</fg=green>');
                } else {
                    if ($isShowDiffOptionEnabled) {
                        $output->write($yamlAlphabeticalChecker->getDifference($pathToYamlFile));
                        return 2;
                    }

                    $errors[] = $pathToYamlFile;
                    $output->writeln('<fg=red>ERROR</fg=red>');
                }
            } catch (ParseException $e) {
                $output->writeln(sprintf('Unable to parse the YAML string: %s', $e->getMessage()));
                return 4;
            }
        }

        $output->writeln('');
        $output->writeln('<fg=green>End of checking yaml files.</fg=green>');

        if (count($errors) > 0) {
            return 2;
        }

        return 0;
    }
}
