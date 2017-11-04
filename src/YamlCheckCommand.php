<?php

namespace YamlAlphabeticalChecker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlCheckCommand extends Command
{
    const
        OPTION_DIR = 'dir',
        OPTION_SHOW_DIFF = 'diff';

    protected function configure()
    {
        $this
            ->setName('yaml-alphabetical-check')
            ->setDescription('Check if yaml files is alphabetically sorted')
            ->addOption(self::OPTION_DIR, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to check')
            ->addOption(self::OPTION_SHOW_DIFF, null, InputOption::VALUE_NONE, 'Show difference in yaml file');
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

        $yamlAlphabeticalChecker = new YamlAlphabeticalChecker();
        $pathToYamlFiles = YamlFilesPathService::getPathToYamlFiles($input->getOption(self::OPTION_DIR));
        $isShowDiffOptionEnabled = $input->getOption(self::OPTION_SHOW_DIFF) === true;

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

                    $output->writeln('<fg=red>ERROR</fg=red>');
                }
            } catch (ParseException $e) {
                $output->writeln(sprintf('Unable to parse the YAML string: %s', $e->getMessage()));
                return 1;
            }
        }

        $output->writeln('');
        $output->writeln('<fg=green>End of checking yaml files.</fg=green>');

        return 0;
    }
}
