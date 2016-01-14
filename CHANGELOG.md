# 3.2.0

## Bug Fixes

- **Console / CLI**: Empty XML-Configs no longer throw an exception
- **Deep comparison**:
  Only the signatures (class-name, function-name, ...) were compared with each another.
  All sub nodes are now used and given to the rule sets / trigger.
- **Changed bodies were not always detected**:
  Due to wrong boolean logic while checking contents of functions
  and methods the "BodyChanged"-Trigger did not always detected changes within the function/method.
- **Match filter against relative path**:
  Pattern were matched against the realpath (e.g. "/var/project/foo/bar.php")
  which will fail when using start and end delimiter like "^name$".
  Only the relative path is compare now (e.g. "foo/bar.php").
- **Proper XML-Path** for some config nodes
  - \PHPSemVer\Config\RuleSet::XPATH corrected,
  was wrong and blocked parsing some configurations.

## Features

### Console

- **Local phpsemver.xml will be used**:
  When there is a "phpsemver.xml" file in your project root,
  then PHPSemVer will automatically load this config instead of the default SemVer2 config.
- **SemVer2 is default rule set when none is given**
- **Progress bar while fetching files**:
  A progress bar is shown in verbose-mode that shows how long the action will take in minutes.
  This is done due to many large projects waiting for the results.
  PHPSemVer now indicates that it is working instead of idling silently.

### Config

- **Choose which files and folders to scan**:
  The `<Filter>` has now a section called `<Whitelist>` to choose which folder shall be parsed
  and compared.
  It works like the `<Blacklist>` filter.

### Rule sets

- Rule set for **Atoum 2** (called "Atoum")
- Rule set for **Drupal 8** (called "Drupal-Core")

### Trigger

- **Interface added**:
  Check if an interface has been added.
  This is included in the SemVer2 Rule as a minor change.
- **Interface removed**:
  Check if an interface has been removed.
  This is included in the SemVer2 Rule as a major change.

## Breaking changes

*There are no known breaking changes.*