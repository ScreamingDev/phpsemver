# PHPSemVer

> Check your changes against semantic versions

[![Build Status](https://travis-ci.org/sourcerer-mike/phpsemver.svg?branch=3.2.0)](https://travis-ci.org/sourcerer-mike/phpsemver)
[![Coverage](https://codecov.io/github/sourcerer-mike/phpsemver/coverage.svg?branch=3.2.0)](http://codecov.io/github/sourcerer-mike/phpsemver?branch=3.2.0)

Install it via composer

    composer require sourcerer-mike/phpsemver

and test your code by comparing two versions

    phpsemver compare 3.2.0 HEAD

or the last commit with your current work:

    phpsemver compare HEAD .
    
    +-------+-------------------------------------------------------------------+
    | Level | Message                                                           |
    +-------+-------------------------------------------------------------------+
    | major | phpsemver_get_composer_config() removed.                          |
    | major | PHPSemVer\Specification removed.                                  |
    | minor | PHPSemVer\Config added.                                           |
    | minor | PHPSemVer\Wrapper\AbstractWrapper::mergeTrees() added.            |
    | patch | PHPSemVer\Wrapper\Directory::getAllFileNames() body changed.      |
    | patch | PHPSemVer\Wrapper\Git::getAllFileNames() body changed.            |
    +-------+-------------------------------------------------------------------+
    
    Total time: 0.94

You're welcome!

## Features

### Commands

Try the several possibilities:

- Use `compare` to check for changes.
- Use `vcs:message` to enhance your commit messages.

### Wrapper

Choose between those wrapper(`phpsemver compare --type ...`):

- GIT
- Filesystem / Directories

If one argument is a directory, then the system will work on the file system.

### Assertions

Make assertions on:

- Functions
	- IsAdded: Check if a function is new.
	- IsRemoved: Check if a function is removed.
	- BodyChanged: Check if someone changed the behaviour of a function.
- Classes
	- IsAdded: Check if a classes is new.
	- IsRemoved: Check if a classes is removed.
- Methods
	- IsAdded: Check if a method is new.
	- IsRemoved: Check if a method is removed.
	- BodyChanged: Check if someone changed the behaviour of a method.
	- ReturnTypeChanged: Watch for changed return types.
	- ReturnTypeRemoved: Watch for incompatible changes on methods.
- Interfaces
    - IsAdded: Check if an interface is new.
    - IsRemoved: Check if an interface is removed.

Combine them as you like in your own configuration file.


## Configuration

Configure which assertions are used in a XML-File.
The delivered XSD file makes it easy to write your own configuration (in a proper IDE).

    <?xml version="1.0"?>
    <phpsemver
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/sourcerer-mike/phpsemver/3.2.0/etc/phpsemver.xsd"
            title="My own rules">
        <RuleSet name="major">
            <Trigger>
                <Functions>
                    <IsRemoved />
                </Functions>
            </Trigger>
        </RuleSet>
        <RuleSet name="minor">
            <Trigger>
                <Classes>
                    <IsAdded />
                </Classes>
            </Trigger>
        </RuleSet>
        <RuleSet name="patch">
    
        </RuleSet>
        <Filter>
            <Whitelist>
                <Pattern>@lib/.*@</Pattern>
            </Whitelist>
            <Blacklist>
                <Pattern>@lib/Test/.*@</Pattern>
            </Blacklist>
        </Filter>
    </phpsemver>

Write your own and use it with the `--ruleset` option.
See the wiki entry for more information: https://github.com/sourcerer-mike/phpsemver/wiki/Configuration

### Prepared rule sets

Those projects do semantic versions in different ways.
So a special config is written for them which can be used via the `--ruleSet` option:

- Drupal-Core
- SemVer2
- WordPress

Just write `phpsemver --ruleSet Drupal-Core` and see the latest changes in Drupal.
There are other companies that follow some semantics in their rules like
Symfony.
Don't drag behind - catch up with PHPSemVer.
