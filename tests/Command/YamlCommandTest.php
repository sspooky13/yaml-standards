<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class YamlCommandTest extends TestCase
{
    public function testCorrectRunCommandForCheckAlphabeticalSort()
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_DIRS_OR_FILES => ['./tests/yamlFiles/sorted/service/symfony-service.yml'],
            '--' . YamlCommand::OPTION_CHECK_ALPHABETICAL_SORT_DEPTH => 2,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckIndent()
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_DIRS_OR_FILES => ['./tests/yamlFiles/sorted/service/symfony-service.yml'],
            '--' . YamlCommand::OPTION_CHECK_YAML_COUNT_OF_INDENTS => 4,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckInline()
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_DIRS_OR_FILES => ['./tests/yamlFiles/sorted/service/symfony-service.yml'],
            '--' . YamlCommand::OPTION_CHECK_INLINE,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckSpaceBetweenGroup()
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_DIRS_OR_FILES => ['./tests/yamlFiles/sorted/service/symfony-service.yml'],
            '--' . YamlCommand::OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS => 2,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForAllChecks()
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_DIRS_OR_FILES => ['./tests/yamlFiles/sorted/service/symfony-service.yml'],
            '--' . YamlCommand::OPTION_CHECK_ALPHABETICAL_SORT_DEPTH => 2,
            '--' . YamlCommand::OPTION_CHECK_YAML_COUNT_OF_INDENTS => 4,
            '--' . YamlCommand::OPTION_CHECK_INLINE,
            '--' . YamlCommand::OPTION_CHECK_LEVEL_FOR_SPACES_BETWEEN_GROUPS => 2,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    /**
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    private function createCommandTester()
    {
        $application = new Application();
        $application->add(new YamlCommand());
        $command = $application->find('yaml-standards');

        return new CommandTester($command);
    }
}
