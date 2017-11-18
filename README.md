# YAML alphabetical checker

[![Latest Stable Version](https://poser.pugx.org/sspooky13/yaml-alphabetical-checker/v/stable)](https://packagist.org/packages/sspooky13/yaml-alphabetical-checker)
[![Total Downloads](https://poser.pugx.org/sspooky13/yaml-alphabetical-checker/downloads)](https://packagist.org/packages/sspooky13/yaml-alphabetical-checker)
[![Build Status](https://travis-ci.org/sspooky13/yaml-alphabetical-checker.svg?branch=master)](https://travis-ci.org/sspooky13/yaml-alphabetical-checker)

This library helps you to keep YAML files alphabetically sorted.

## Requirements
Works with minimal requirement:
- PHP 5.6 or higher
- symfony/console version 3.0
- symfony/yaml version 3.0
- sebastian/diff version 1.4

## Installation
If you prefer using [Composer](http://getcomposer.org/) you can easily install with the following command:

    composer require --dev sspooky13/yaml-alphabetical-checker
    
Or alternatively, include a dependency for sspooky13/yaml-alphabetical-checker in your composer.json file. For example:

```json
{
    "require-dev": {
        "sspooky13/yaml-alphabetical-checker": "~2.0"
    }
}
```
## Options for run
- `--exclude=text` Exclude file contains the `text` in name. Can be used more times.

## Usage
For run from command line:

    ./bin/yaml-alphabetical-checker ./app ./src/path/to/config/file.yml ./src --exclude=serv --exclude=conf

If you need exclude a files from check, you can print command `--exclude=NAME` how much you want.

or setting for ANT:

```xml
<property name="path.yaml-alphabetical-checker" value="./vendor/bin/yaml-alphabetical-checker"/>

<target name="yaml-alphabetical-checker" description="Run yaml alphabetical checker">
    <exec 
        executable="${path.yaml-alphabetical-checker}"
        logoutput="true"
        passthru="true"
        checkreturn="true"
    >
        <arg value="./app" />
        <arg value="./src/path/to/config/file.yml" />
        <arg value="./src" />
        <arg value="--exclude=serv" />
        <arg value="--exclude=conf" />
    </exec>
</target>
```

## Exit codes
Exit code is built using following bit flags:

    0 OK.
    1 Some file is unsorted.
    2 General error (file is not readable, error with parse yaml file).