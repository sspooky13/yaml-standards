# YAML standards

[![Total Downloads](https://poser.pugx.org/sspooky13/yaml-standards/downloads)](https://packagist.org/packages/sspooky13/yaml-standards)
[![Build Status](https://github.com/sspooky13/yaml-standards/actions/workflows/build.yaml/badge.svg)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/74e686c9111940a982ff3ee5e4ca9d52)](https://www.codacy.com/app/sspooky13/yaml-standards?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=sspooky13/yaml-standards&amp;utm_campaign=Badge_Grade)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sspooky13/yaml-standards/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sspooky13/yaml-standards/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sspooky13/yaml-standards/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sspooky13/yaml-standards/?branch=master)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sspooky13_yaml-standards&metric=alert_status)](https://sonarcloud.io/dashboard?id=sspooky13_yaml-standards)
![PHPStan level](https://img.shields.io/badge/PHPStan-level%205-brightgreen.svg)

This library is primarily intended to help you to keep observe standards for YAML files, but some standards can be used for other files, e.g. **YamlEmptyLineAtEnd** standard

## Installation
Install the latest version with [Composer](http://getcomposer.org/) command:

    composer require --dev sspooky13/yaml-standards

## Usage
1. Create config file in project root directory with allowed standards and files/directories to check. You can copy config file `./example/yaml-standards.yml` and edit it according to your needs.
2. Run `vendor/bin/yaml-standards`

Tips:
- If your config file has different name or it's located in different directory as root you can run command with argument where is wried path to config file with name, e.g. `vendor/bin/yaml-standards ./path/to/your/configFile.yaml`
- You can create target for [Phing](https://www.phing.info/) build tool, e.g.

```xml
<property name="path.yaml-standards" value="./vendor/bin/yaml-standards"/>

<target name="check-yaml-standards" description="Run yaml standards checks">
    <exec
        executable="${path.yaml-standards}"
        logoutput="true"
        passthru="true"
        checkreturn="true"
    >
        <arg value="./path/to/your/configFile.yaml" />
    </exec>
</target>
```

## Options for run
- `./path/to/your/configFile.yaml` Path to your config file. Default is `./yaml-standards.yaml`.
- `--fix` Automatically fix allowed standards problems.

## Implemented checkers
- **YamlAlphabeticalChecker** - Check yaml file is alphabetically sorted to selected level. **This checker has fixer**.
- **YamlIndentChecker** - Check yaml has right count of indents. **This checker has fixer**.
- **YamlSpacesBetweenGroupsChecker** - Check yaml file has empty line between every group to selected level. **This checker has fixer**.
- **YamlInlineChecker** - Check yaml file observe standards by symfony yaml parser.
- **YamlEmptyLineAtEnd** - Check yaml file has empty line at end of file. **This checker has fixer**. Note: This standard can be used on every file, not only yaml files.
- **YamlServiceAliasing** - Check yaml service file observe short or long code style aliasing. **This checker has fixer**.

## PHPStorm Integration
You can integrate YAML standards into PHPStorm by using File Watcher.

1. Open Settings -> Tools -> File Watchers
2. Add new -> custom
3. Give it a name
4. Select file type: `YAML`
5. Program: `\vendor\bin\yaml-standards.bat`
6. Arguments: absolute path to your config file
7. In config file path to check and excluded paths must have absolute path too

Now, file watcher check your YAML files by config file and notify you if they have errors

## How create your own standards
1. Create class with your own check/fix logic
2. Checker has to extend class `YamlStandards\Model\AbstractChecker.php` and class name have to end with `Checker` word, e.g. YamlLine**Checker**
3. Fixer has to be in same directory as checker class, extend class `YamlStandards\Model\AbstractFixer.php` and name have to be same as checker class except name must end with `Fixer` word, e.g. YamlLine**Fixer**. **Warning! checker class must exist too.**
4. Both classes must return class `\YamlStandards\Result\Result`
5. Add your checker class with namespace to your config file to `checkers` array
6. done :)

**If you think your checker/fixer can be helpful for others, you can create pull request with your code to make it available to everyone :)**

## Exit codes
Exit code is built using following bit flags:

    0 OK.
    1 Some file has invalid syntax.
    2 General error (file is not readable, error with parse yaml file).
