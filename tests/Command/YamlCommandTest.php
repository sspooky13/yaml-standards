<?php

declare(strict_types=1);

namespace YamlStandards\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class YamlCommandTest extends TestCase
{
    public function testCorrectRunCommandForCheckAlphabeticalSort(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-alphabetical.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckIndent(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-indent.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckInline(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-inline.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckSpaceBetweenGroup(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-space-between-groups.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckEmptyLineAtEnd(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-empty-line-at-end.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForCheckServiceAliasing(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-service-aliasing.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForExcludedPaths(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-exclude.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForFix(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-all.yaml',
            '--' . YamlCommand::OPTION_FIX => true,
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testCorrectRunCommandForAllChecks(): void
    {
        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-all.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    public function testRunCommandWithoutSuffixThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $command = $this->createCommandTester();
        $commandExitCode = $command->execute([
            YamlCommand::ARGUMENT_PATH_TO_CONFIG_FILE => __DIR__ . '/resource/yaml-standards-all-without-suffix.yaml',
            '--' . YamlCommand::OPTION_DISABLE_CACHE => true,
        ]);

        $this->assertEquals(0, $commandExitCode);
    }

    /**
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    private function createCommandTester(): CommandTester
    {
        $application = new Application();

        // Symfony 8.0+ removed add() method in favor of addCommand()
        // For backward compatibility with Symfony 4.2-7.x, we check which method exists
        if (method_exists($application, 'addCommand')) {
            $application->addCommand(new YamlCommand());
        } else {
            $application->add(new YamlCommand());
        }

        $command = $application->find('yaml-standards');

        return new CommandTester($command);
    }
}
