# Changelog

## [Unreleased]

## [9.0.0]
### Added
- added support for Symfony 7
- [#77] CI: add build PHP 8.3 and Symfony 7
- [#84] added support for sebastian/diff version 6, Thanks to [@TomasLudvik]

### Removed
- [#69][#75] dropped support PHP 7.1 and dropped support Symfony 4.1 and lower

### Fixed
- [#80] fixed PHP Deprecated: Use of "self" in callables is deprecated, Thanks to [@TomasLudvik]

## [8.1.1]
### Fixed
- [#83] fixed application when using sebastian/diff version 5 (set Builder class for Differ class)

## [8.1.0]
### Added
- [#79] Added option to hide progress bar, Thanks to [@techi602]

### Changed
- [#78] Use system temp dir as default cache dir instead of root of project, Thanks to [@techi602]

## [8.0.1]
### Fixed
- [#76] Files path service: only use GLOB_BRACE when available

## [8.0.0] - 2023-02-26
### Added
- add support for Symfony 6
- Config: added new parameter for ignoring indent for comments for indent check
- [#55] added option to disable check only changed files and added option to change path to cache file
- [#70] CI: add build PHP 8.2
- [#72] Composer: added support to "sebastian/diff" version 5

### Changed
- removed status code from result class, now it's no longer necessary

### Removed
- [#71] Composer: removed dependency to "symfony/http-kernel"

### Fixed
- [#46] CI: add support for PHP 8.1 and Symfony 5 and 6
- [#73] Command: show information about file is not readable

## [7.0.1] - 2021-12-12
### Changed
- [#64] Changed names of classes used in yaml files in tests

## [7.0.0] - 2021-09-02
### Added
- add support for PHP 8

### Removed
- drop support for symfony 3.4.30 and lower

## [6.0.0] - 2021-03-21
### Added
- Yaml alphabetical: create fixer
- Config: added new parameter for prioritizing keys in alphabetical functionality

### Changed
- Yaml files path service: use wildcards for find files instead of directory recursive iterator
- Yaml empty line at end: this standard can be used for any file suffix
- Config: prioritized keys can be defined exactly with string `::exact` for exact search

### Fixed
- Yaml indent: fix get correct indents for array
```diff
apiVersion2: v1
    -   addresses:
            -   ip: ~
        ports:
+           -   name: postgres
+               port: 5432
-                -   name: postgres
-                    port: 5432
```
- Yaml files path service: now you can use curly braces to find file, e.g.: `services.{yml,yaml}`
- Yaml alphabetical: fixed right sort yaml by depth
- Yaml files path service: don't run recursive searching for file if full path to file is defined
- Yaml indent: fix get correct indents for array
```diff
plugins:
    - search
    -   readthedocs-version-warning:
            project_id: "490215"
            show_on_versions:
-           - latest
+               - latest
```

## [5.1.2] - 2020-02-02
### Fixed
- Yaml service aliasing: get correct short type if alias has double quotes

## [5.1.1] - 2019-12-27
### Fixed
- Yaml empty line at end: correct fix file with one non-blank line

## [5.1.0] - 2019-12-26
### Added
- Config: add new parameter for preserve indents comment lines without parent
    - Yaml indents: don't change indents for comment line without parent if is set 'preserved' parameter

### Fixed
- Yaml indent: fix issue with file ending with comment line

## [5.0.0] - 2019-12-15
### Added
- create fixer for yaml spaces between groups
- from now project can set different and more options for yaml standards
    - create config file with options
- whoever can create own checker and fixer and add it to config file
- create new standards: YamlEmptyLineAtEnd
    - Check yaml file has empty line at end of file
- create new standards: YamlServiceAliasing
    - Check yaml service file observe short or long code style aliasing
- added support for symfony 5

### Changed
- remove command options and move it to config file
- config: all parameters are now optional and have default value

### Removed
- remove support for php 5.6 version and php 7.0 version
- exclude files by name is no longer implemented

### Fixed
- Yaml indent: fix scenario for array as it is used in example file `example/yaml-standards.yaml`

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

[@TomasLudvik]: https://github.com/TomasLudvik
[@techi602]: https://github.com/techi602
[@ChrisDBrown]: https://github.com/ChrisDBrown
[@boris-brtan]: https://github.com/boris-brtan
[@DavidOstrozlik]: https://github.com/DavidOstrozlik
[@PetrHeinz]: https://github.com/PetrHeinz

[#84]: https://github.com/sspooky13/yaml-standards/pull/84/files
[#83]: https://github.com/sspooky13/yaml-standards/issues/83
[#80]: https://github.com/sspooky13/yaml-standards/pull/80
[#79]: https://github.com/sspooky13/yaml-standards/pull/79
[#78]: https://github.com/sspooky13/yaml-standards/pull/78
[#77]: https://github.com/sspooky13/yaml-standards/issues/77
[#76]: https://github.com/sspooky13/yaml-standards/issues/76
[#75]: https://github.com/sspooky13/yaml-standards/issues/75
[#73]: https://github.com/sspooky13/yaml-standards/issues/73
[#72]: https://github.com/sspooky13/yaml-standards/issues/72
[#71]: https://github.com/sspooky13/yaml-standards/issues/71
[#70]: https://github.com/sspooky13/yaml-standards/issues/70
[#69]: https://github.com/sspooky13/yaml-standards/issues/69
[#64]: https://github.com/sspooky13/yaml-standards/issues/64
[#55]: https://github.com/sspooky13/yaml-standards/issues/55
[#46]: https://github.com/sspooky13/yaml-standards/issues/46
[#35]: https://github.com/sspooky13/yaml-standards/pull/35
[#32]: https://github.com/sspooky13/yaml-standards/pull/32
[#31]: https://github.com/sspooky13/yaml-standards/pull/31
[#27]: https://github.com/sspooky13/yaml-standards/pull/27
[#18]: https://github.com/sspooky13/yaml-standards/pull/18
[#14]: https://github.com/sspooky13/yaml-standards/issues/14
[#13]: https://github.com/sspooky13/yaml-standards/pull/13

[Unreleased]: https://github.com/sspooky13/yaml-standards/compare/9.0.0...HEAD
[9.0.0]: https://github.com/sspooky13/yaml-standards/compare/8.1.1...9.0.0
[8.1.1]: https://github.com/sspooky13/yaml-standards/compare/8.1.0...8.1.1
[8.1.0]: https://github.com/sspooky13/yaml-standards/compare/8.0.1...8.1.0
[8.0.1]: https://github.com/sspooky13/yaml-standards/compare/8.0.0...8.0.1
[8.0.0]: https://github.com/sspooky13/yaml-standards/compare/7.0.1...8.0.0
[7.0.1]: https://github.com/sspooky13/yaml-standards/compare/7.0.0...7.0.1
[7.0.0]: https://github.com/sspooky13/yaml-standards/compare/6.0.0...7.0.0
[6.0.0]: https://github.com/sspooky13/yaml-standards/compare/5.1.2...6.0.0
[5.1.2]: https://github.com/sspooky13/yaml-standards/compare/5.1.1...5.1.2
[5.1.1]: https://github.com/sspooky13/yaml-standards/compare/5.1.0...5.1.1
[5.1.0]: https://github.com/sspooky13/yaml-standards/compare/5.0.0...5.1.0
[5.0.0]: https://github.com/sspooky13/yaml-standards/compare/4.2.5...5.0.0
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
