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

## Releases

Are made using semantic versions.

### Major

Those releases are incompatible with their previous.
Migration is only guaranteed from one specific minor version.

- [ ] Doc: There should be an API documentation.
- [ ] PHPUnit: No incomplete tests
- [ ] PHPUnit: No skipped tests
- [ ] PHPUnit: Code coverage should be over 90% in Methods and Lines.

### Minor

Such releases are compatible with their previous minor version.

Assertions before the release:

- [ ] `git branch -a --no-merge` should not contain features that are meant for the release.
- [ ] PHPUnit Code Coverage should be over 90% in Lines.
- [ ] PHPUnit should have no incomplete tests.
- [ ] PHPSemVer should not show any major change.
- [ ] The support-branch should have a description (`git branch --edit-description`) with a change log,
  upgrade and downgrade notices to the nearest minor or major version.

And everything from patches.

### Patch

This harms no one.

- [ ] PHPUnit Code Coverage should be over 80% in Lines.
- [ ] PHPUnit must have no errors
- [ ] PHPSemVer must not show any minor change.
- [ ] The version-tag must contain git-notes with a change log (`git flow` helps).
  You may enumerate the changes using the [semantic commit] (https://gist.github.com/sourcerer-mike/9629666).
  Hint: A diff of the phpunit testdox might help.
- [ ] Some support-branch description should be updated:
	- [ ] The next lower support-branch should contain upgrade notices.
	- [ ] The next higher support-branch should contain downgrade notices.
	- [ ] The support-branch description of the minor version should be extended by the change log.
- [ ] PHPMD: No warnings or errors
- [ ] PHPCS: No warnings or errors