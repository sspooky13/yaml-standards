# YAML alphabetical checker

[![Latest Stable Version](https://poser.pugx.org/sspooky13/yaml-alphabetical-checker/v/stable)](https://packagist.org/packages/sspooky13/yaml-alphabetical-checker)
[![Total Downloads](https://poser.pugx.org/sspooky13/yaml-alphabetical-checker/downloads)](https://packagist.org/packages/sspooky13/yaml-alphabetical-checker)
[![Build Status](https://travis-ci.org/sspooky13/yaml-alphabetical-checker.svg?branch=master)](https://travis-ci.org/sspooky13/yaml-alphabetical-checker)

This library helps you to keep YAML file alphabetically sorted.

## Requirements
Works with minimal requirement:
- PHP 5.6 or higher
- symfony/console version 3.0
- symfony/yaml version 3.0

## Installation
If you prefer using [Composer](http://getcomposer.org/) you can easily install with the following command:

    composer require --dev sspooky13/yaml-alphabetical-checker
    
Or alternatively, include a dependency for sspooky13/yaml-alphabetical-checker in your composer.json file. For example:

```json
{
    "require-dev": {
        "sspooky13/yaml-alphabetical-checker": "~1.0"
    }
}
```
## Options for run
- `--diff` Show right sort in next unsorted file.

## Usage
For run from command line:

    ./bin/yaml-alphabetical-checker --dir=./app --dir=./src.

If you need check more directory, you can add dirs call `--dir=PATH_TO_DIR` how much you want.

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
        <arg path="--dir=./app" />
        <arg path="--dir=./src" />
    </exec>
</target>
```

## Exit codes
Exit code is built using following bit flags:

    0 Basic functionality.
    1 General error (file is not readable, error with parse yaml file).
    2 Some file is unsorted (only in diff mode).