# PHPSemVer - Check your changes against semantic versions

## Example

    ./bin/phpsemver compare HEAD~3 .
    
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

