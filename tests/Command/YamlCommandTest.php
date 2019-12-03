<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class YamlCommandTest extends TestCase
{
    public function testCorrectRunCommandForCheckAlphabeticalSort(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-alphabetical.yaml',
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckIndent(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-indent.yaml',
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckInline(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-inline.yaml',
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckSpaceBetweenGroup(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-space-between-groups.yaml',
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckEmptyLineAtEnd(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-empty-line-at-end.yaml',
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForExcludedPaths(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-exclude.yaml',
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForFix(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-all.yaml',
            '--' . YamlCommand::OPTION_FIX,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForAllChecks(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-all.yaml',
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    /**
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    private function createCommandTester(): CommandTester
    {
        $application = new Application();
        $application->add(new YamlCommand());
        $command = $application->find('yaml-standards');

        return new CommandTester($command);
    }
}
