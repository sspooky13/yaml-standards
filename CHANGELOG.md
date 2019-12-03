# Changelog

## [Unreleased]
### Added
- create fixer for yaml spaces between groups
- from now project can set different and more options for yaml standards
    - create config file with options
- whoever can create own checker and fixer and add it to config file
- create new standards: YamlEmptyLineAtEnd
    - Check yaml file has empty line at end of file

### Changed
- remove command options and move it to config file

### Removed
- remove support for php 5.6 version and php 7.0 version
- exclude files by name is no longer implemented

## [4.2.5] - 2019-08-13
### Changed
- [#31] Yaml files path service: ignore uniterable filepaths, Thanks to [@PetrHeinz]

### Fixed
- [#32] Yaml indent: fix situation when key is without value and is not parent, Thanks to [@PetrHeinz]
- [#35] Yaml indent: fix right indent for arrays with unquoted colons, Thanks to [@PetrHeinz]

## [4.2.4] - 2019-07-26
### Fixed
- [#27] Yaml indent: fix nested hierarchy, where elements are nested in 2 arrays, e.g.:
```yaml
patchesJson6902:
-   target:
        group: extensions
        version: v1beta1
        kind: Ingress
        name: shopsys
    path: ./ingress-patch.yaml
```
to correct
```yaml
patchesJson6902:
    -   target:
            group: extensions
            version: v1beta1
            kind: Ingress
            name: shopsys
        path: ./ingress-patch.yaml
```
, Thanks to [@PetrHeinz]

## [4.2.3] - 2019-07-14
### Fixed
- Yaml path service: first check whether path refer to real dir or file

## [4.2.2] - 2019-06-18
### Fixed
- fix don't check skipped files

## [4.2.1] - 2019-06-02
### Fixed
- caught exception thrown while loading directories by recursion 

## [4.2.0] - 2019-06-02
### Added
- show skipped files in terminal
- add `--fix` option
- create fixer for yaml indent
- show info what problem can be fixed by fixer

### Fixed
- Yaml indent checker: fix get correct indents between dash and key if line belong to array and has zero indents
- Yaml indent checker: fix counting of parents if line belong to array and has bad zero indents and simultaneously is child of higher parent
- Yaml inline checker: fix show different between recommended and actual file content
- Yaml indent checker: fix get correct indents when line is directive

## [4.1.0] - 2019-03-07
### Added
- [#13] Output result: output now adapts to the actual window width of the terminal, Thanks to [@boris-brtan]
- add option `exclude-file` for exclude files for check

### Fixed
- [#14] Yaml indent checker: add right indents for comment line without in bottom, Thanks to [@DavidOstrozlik]
- [#18] Add missing necessary `jakub-onderka/php-parallel-lint` to composer require, Thanks to [@ChrisDBrown]

## [4.0.0] - 2019-01-21
### Added
- add option `exclude-dir` for exclude dirs for check

### Changed
- rename option from `exclude` to `exclude-by-name`

## [3.1.1] - 2018-12-09
### Fixed
- Yaml indent checker: fix check correct indent

## [3.1.0] - 2018-11-17
### Added
- Yaml indent checker: check also correct indent between dash and text in one line array

### Fixed
- fix showing code status for current checked file
- Yaml indent checker: fix correct indent for reused variable
- Yaml indent checker: fix correct indent for array in one line, e.g. `- foo: bar` or `- { foo: bar }` and their children

## [3.0.0] - 2018-10-21
### Added
- add `.yaml` file support
- Indent checker:
    - create new checker to check right count of intents
    - add new option, e.g. `--check-indents-count-of-indents=4`
- Inline checker:
    - create new checker to check correct style of yaml file
    - add new option, e.g. `--check-inline`
- Spaces between groups checker:
    - create new checker to check space between groups
    - add new option, e.g. `--check-spaces-between-groups-to-level=2`
- Alphabetical checker:
    - add new option for check depth correct alphabetically sort, e.g. `--check-alphabetical-sort-depth=2`

### Changed
- change yaml dump inline to highest in alphabetical checker
- command is now lazy loaded
- remove unnecessary indent from alphabetical checker
- rename project from yaml-alphabetical-checker to yaml-standards

### Fixed
- if file is not readable, then continue to next file

## [2.0.0] - 2017-11-18
### Added
- add new option for exclude files with mask, e.g. `--exclude=service`

### Changed
- option directories changed to required argument
- now you can send file path and directories for argument to check

### Removed
- remove `classic` mode and replace him for `diff` mode

## [1.0.1] - 2017-11-05
### Fixed
- fix return exit code in classic mode

## 1.0.0 - 2017-11-04
### Added
- create base command to check yaml sort
- create `--diff` mode

[@ChrisDBrown]: https://github.com/ChrisDBrown
[@boris-brtan]: https://github.com/boris-brtan
[@DavidOstrozlik]: https://github.com/DavidOstrozlik
[@PetrHeinz]: https://github.com/PetrHeinz

[#35]: https://github.com/sspooky13/yaml-standards/pull/35
[#32]: https://github.com/sspooky13/yaml-standards/pull/32
[#31]: https://github.com/sspooky13/yaml-standards/pull/31
[#27]: https://github.com/sspooky13/yaml-standards/pull/27
[#18]: https://github.com/sspooky13/yaml-standards/pull/18
[#14]: https://github.com/sspooky13/yaml-standards/issues/14
[#13]: https://github.com/sspooky13/yaml-standards/pull/13

[Unreleased]: https://github.com/sspooky13/yaml-standards/compare/4.2.5...HEAD
[4.2.5]: https://github.com/sspooky13/yaml-standards/compare/4.2.4...4.2.5
[4.2.4]: https://github.com/sspooky13/yaml-standards/compare/4.2.3...4.2.4
[4.2.3]: https://github.com/sspooky13/yaml-standards/compare/4.2.2...4.2.3
[4.2.2]: https://github.com/sspooky13/yaml-standards/compare/4.2.1...4.2.2
[4.2.1]: https://github.com/sspooky13/yaml-standards/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/sspooky13/yaml-standards/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/sspooky13/yaml-standards/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/sspooky13/yaml-standards/compare/3.1.1...4.0.0
[3.1.1]: https://github.com/sspooky13/yaml-standards/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/sspooky13/yaml-standards/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/sspooky13/yaml-standards/compare/2.0.0...3.0.0
[2.0.0]: https://github.com/sspooky13/yaml-standards/compare/1.0.1...2.0.0
[1.0.1]: https://github.com/sspooky13/yaml-standards/compare/1.0.0...1.0.1
