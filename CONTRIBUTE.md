# Thanks for contributing!

Just fork, write and ask for merge request.
As usual ;)

## Default project folder structure

Later on you can configure the folder structure as needed.
By default it follows the
[Filesystem Hierarchy Standard 2.3 (FHS, 2004-01-28)] (http://www.pathname.com/fhs/)
to present a structure that is well known to developers all around the world (except "vendor" directory).
The word "will" means that those folders are not part of the repository
and are created later on (by Composer, PHing or other deployment script).

- /bin contains executable commands
- /etc contains configuration for tools
- /lib contains the source files
- /vendor will contain additional applications
- /var will contain changeable and generated files


## Testing

- PHPSpec checks the interface.
- PHPCS checks the coding standards (mixture of PEAR and Symfony)
- PHPMD checks for bad code.
- PHPUnit does UnitTests and Integration-Tests (planned to be done by "bats" or "behat")