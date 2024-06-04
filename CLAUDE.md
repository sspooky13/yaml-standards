# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

YAML Standards is a PHP library for checking and automatically fixing YAML file standards. It's built using Symfony Console and includes multiple standards checkers (alphabetical sorting, indentation, empty lines, service aliasing, etc.) with automatic fixing capabilities.

## Development Commands

### Building and Testing
- `php phing build-ci` - Run CI build (standards checks + tests)
- `php phing tests-unit` - Run unit tests
- `vendor/bin/phpunit --testsuite Unit` - Run PHPUnit tests directly

### Code Quality
- `php phing standards` - Run all code quality checks (lint, ECS, PHPStan)
- `php phing standards-fix` - Auto-fix coding standards violations
- `php phing standards-diff` - Check standards on changed files only
- `vendor/bin/ecs check --fix` - Run Easy Coding Standard with auto-fix
- `vendor/bin/phpstan analyze` - Run static analysis

### Running the Tool
- `vendor/bin/yaml-standards` - Run with default config (yaml-standards.yaml)
- `vendor/bin/yaml-standards path/to/config.yaml` - Run with custom config
- `vendor/bin/yaml-standards --fix` - Run with automatic fixing enabled

### Docker Development
- `make start` - Start Docker containers
- `make exe` - Access PHP container bash
- `make st` - Run standards checks in Docker

## Architecture

### Core Components

**Command Layer** (`src/Command/`)
- `YamlCommand` - Main CLI command handling input/output
- `Service/FilesPathService` - File discovery and path handling
- `Service/ResultService` - Result formatting and display

**Standards Framework** (`src/Model/`)
- `AbstractChecker` - Base class for all standards checkers
- `AbstractFixer` - Base class for automatic fixers  
- `CheckerInterface`/`FixerInterface` - Contracts for standards

**Standards Implementations** (`src/Model/Yaml*/`)
Each standard has its own namespace with Checker, Fixer, and DataFactory:
- `YamlAlphabetical` - Alphabetical sorting of YAML keys
- `YamlIndent` - Proper indentation checking/fixing
- `YamlEmptyLineAtEnd` - Empty line at end of files
- `YamlServiceAliasing` - Symfony service aliasing format
- `YamlServiceArgument` - Service argument format (gradually vs specifically)
- `YamlSpacesBetweenGroups` - Empty lines between groups
- `YamlInline` - Inline format validation

**Configuration** (`src/Model/Config/`)
- `YamlStandardConfigLoader` - Loads and validates config files
- `YamlStandardConfigDefinition` - Symfony Config component setup
- Various data classes for configuration structure

**Utilities** (`src/Model/Component/`)
- `YamlService` - Main YAML processing orchestrator
- `Parser/YamlParser` - Custom YAML parsing with line tracking
- `Cache/` - File-based caching system

### Key Patterns

1. **Checker/Fixer Pattern**: Each standard implements both a checker (validates) and fixer (repairs) following consistent interfaces
2. **Configuration-Driven**: All standards and file paths defined in YAML config files
3. **Caching**: File modification-based caching to avoid re-processing unchanged files
4. **Result Aggregation**: All standards return `Result` objects that get aggregated for final output

### Test Structure
- Unit tests mirror the `src/` structure in `tests/`
- Each standard has test resources in `resource/fixed/` and `resource/unfixed/` directories
- Tests verify both checking and fixing capabilities

## Important Notes

- The tool supports both checking (validation) and fixing (automatic repair) modes
- Standards can be selectively enabled/disabled via configuration
- File discovery supports glob patterns and exclusions
- Caching is enabled by default but can be disabled with `--no-cache`
- Exit codes follow bit flags: 0=OK, 1=syntax errors, 2=general errors

## YamlServiceArgument Standard

This standard checks and fixes Symfony service argument format:
- **Gradually**: Arguments as array items: `- value1`, `- value2`
- **Specifically**: Arguments as named parameters: `$param1: value1`, `$param2: value2`

The argument format is **configurable** via the `serviceArgumentType` parameter (gradually/specifically).

### Implementation Status
- ✅ Checker: `YamlServiceArgumentChecker` - validates argument format
- ✅ Fixer: `YamlServiceArgumentFixer` - converts between formats
- ✅ DataFactory: `YamlServiceArgumentDataFactory` - core transformation logic
- ✅ Configuration: `serviceArgumentType` parameter supports both 'gradually' and 'specifically'
- ✅ Tests: Full test coverage for both formats with mock services

### Testing Notes
- Tests use `YamlStandards\TestService\*` mock classes
- Test files: `simple-service.yml` (unfixed/fixed pairs)
- Run tests: `docker exec yaml-standards-php-fpm vendor/bin/phpunit tests/Model/YamlServiceArgument/`
