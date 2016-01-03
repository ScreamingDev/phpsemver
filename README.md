# PHPSemVer

> Check your changes against semantic versions

[![Build Status](https://travis-ci.org/sourcerer-mike/phpsemver.svg?branch=master)](https://travis-ci.org/sourcerer-mike/phpsemver)
[![Coverage](https://codecov.io/github/sourcerer-mike/phpsemver/coverage.svg?branch=master)](http://codecov.io/github/sourcerer-mike/phpsemver?branch=master)

Install it via composer

    composer require sourcerer-mike/phpsemver

and test your code by comparing two versions

    phpsemver compare 1.0.0 HEAD

## Example

Compare the last commit with your current work:

    ./bin/phpsemver compare HEAD .
    
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

Or some version (git-tag) against the latest changes:

    bin/phpsemver compare 1.0.0 HEAD

You're welcome!

## Features

### Wrapper

Choose between those wrapper(`phpsemver compare --type ...`):

- GIT
- Filesystem / Directories

If one argument is a directory, then the system will work on the file system.

### Assertions

Make assertions on:

- Functions
	- IsAdded: Check if a function is new.
	- IsRemoved: Check if a functions is removed.
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

Tells you which are major, minor or patch changes.

### Configuration

Configure which assertions are used in a XML-File.
The delivered XSD file makes it easy to write your own configuration (in a proper IDE).

    <?xml version="1.0"?>
    <phpsemver
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/sourcerer-mike/phpsemver/3.1.0/etc/phpsemver.xsd"
            title="My own versioning">
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
            <Blacklist>
                <Pattern>@vendor/.*@</Pattern>
                <Pattern>@lib/Test/.*@</Pattern>
                <Pattern>@spec/.*@</Pattern>
            </Blacklist>
        </Filter>
    </phpsemver>

Write your own and use it with the `--ruleset` option.
