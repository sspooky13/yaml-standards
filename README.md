# YAML standards

[![Total Downloads](https://poser.pugx.org/sspooky13/yaml-standards/downloads)](https://packagist.org/packages/sspooky13/yaml-standards)
[![Build Status](https://travis-ci.org/sspooky13/yaml-standards.svg?branch=master)](https://travis-ci.org/sspooky13/yaml-standards)
[![Build status](https://ci.appveyor.com/api/projects/status/gqcvrvg1hb0g6r0c/branch/master?svg=true)](https://ci.appveyor.com/project/sspooky13/yaml-standards/branch/master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/74e686c9111940a982ff3ee5e4ca9d52)](https://www.codacy.com/app/sspooky13/yaml-standards?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=sspooky13/yaml-standards&amp;utm_campaign=Badge_Grade)

This library helps you to keep YAML files alphabetically sorted, observe symfony yaml standards, observe indent and observe spaces between groups.

## Installation
If you prefer using [Composer](http://getcomposer.org/) you can easily install with the following command:

    composer require --dev sspooky13/yaml-standards
    
Or alternatively, include a dependency for sspooky13/yaml-standards in your composer.json file. For example:

```json
{
    "require-dev": {
        "sspooky13/yaml-standards": "~3.0"
    }
}
```
## Options for run
- `--exclude-by-name=text` Exclude file contains the `text` in name. Can be used more times.
- `--exclude-dir=path/to/excluded/dir` Exclude dir from check. Can be used more times.
- `--exclude-file=path/to/excluded/file.yaml` Exclude file from check. Can be used more times.
- `--check-alphabetical-sort-depth=2` Check yaml file is alphabetically sorted to selected level.
- `--check-indents-count-of-indents=4` Check yaml has multiple of selected indent
- `--check-inline` Check yaml file observe standards by symfony yaml parser.
- `--check-spaces-between-groups-to-level=2` Check yaml file has empty line between every group to slected level.

## Usage
For run from command line:

    php bin/yaml-standards ./app ./src/path/to/config/file.yml ./src --exclude=service --check-alphabetical-sort-depth=2 --check-indents-count-of-indents=4 --check-spaces-between-groups-to-level=2 --check-inline


If you need exclude a files from check, you can print command `--exclude=NAME` how much you want.

or setting for ANT:

```xml
<property name="path.yaml-standards" value="./vendor/bin/yaml-standards"/>

<target name="yaml-standards" description="Run yaml standards checks">
    <exec 
        executable="${path.yaml-standards}"
        logoutput="true"
        passthru="true"
        checkreturn="true"
    >
        <arg value="./app" />
        <arg value="./src/path/to/config/file.yml" />
        <arg value="./src" />
        <arg value="--exclude=service" />
        <arg value="--exclude-dirs=path/to/excluded/dir" />
        <arg value="--exclude-file=path/to/excluded/file.yaml" />
        <arg value="--check-alphabetical-sort-depth=2" />
        <arg value="--check-indents-count-of-indents=4" />
        <arg value="--check-spaces-between-groups-to-level=2" />
        <arg value="--check-inline" />
    </exec>
</target>
```

## PHPStorm Integration
You can integrate YAML standards into PHPStorm by using File Watcher.

1. Open Settings -> Tools -> File Watchers
2. Add new -> custom
3. Give it a name
4. Select file type: `YAML`
5. Program: `\vendor\bin\yaml-standards.bat`
6. Arguments: `$FilePath$` for check actual opened file or `$SourcepathEntry$` for check all source file, etc.
7. Set into arguments too what want you check, e.g. check alphabetical sort: `$FilePath$ --check-alphabetical-sort-depth=2`

Now, file watcher check YAML files, whether is right alphabetically sorted and will open the console if they have errors

## Exit codes
Exit code is built using following bit flags:

    0 OK.
    1 Some file has invalid syntax.
    2 General error (file is not readable, error with parse yaml file).
