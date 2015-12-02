# PHPSemVer 2

> Check your changes against semantic versions

[![Build Status](https://travis-ci.org/sourcerer-mike/phpsemver.svg?branch=master)](https://travis-ci.org/sourcerer-mike/phpsemver)
[![Coverage](http://codecov.io/github/sourcerer-mike/phpsemver/coverage.svg?branch=master)](http://codecov.io/github/sourcerer-mike/phpsemver?branch=master)

## Yadda

Available source wrapper:

- GIT
- Filesystem

Assertions on:

- Namespaces
- Classes
- Methods
- Functions

Tells you which are major, minor or patch changes.


## Example

Compare the last commit with your current work:

    ./bin/phpsemver compare HEAD .
    
    +-------+-----------------------------------------------------------------+
    | Level | Message                                                         |
    +-------+-----------------------------------------------------------------+
    | major | Removed class "PHPSemVer\Assertions\ErrorMessage".              |
    | major | Removed method "PHPSemVer\Wrapper\AbstractWrapper::getBuilder". |
    | minor | Added namespace "PHPSemVer\DataTree".                           |
    | minor | Added class "PHPSemVer\Console\ParseCommand".                   |
    | minor | Added method "PHPSemVer\Wrapper\AbstractWrapper::getDataTree".  |
    +-------+-----------------------------------------------------------------+
    Done!

Or some version (git-tag) against the latest changes:

    bin/phpsemver compare v1.0.0 HEAD

You're welcome!
