# Thanks for contributing!

## Default project folder structure

Later on you can configure the folder structure as needed.
By default it follows the
[Filesystem Hierarchy Standard 2.3 (FHS, 2004-01-28)] (http://www.pathname.com/fhs/)
to present a structure that is well known to developers all around the world.
The word "will" means that those folders are not part of the repository
and will be created later on (by Composer, PHing or other deployment script).

- /bin contains executable commands
- /etc contains
- /lib contains the source files
- /opt will contain additional applications
- /var will contain changeable and generated files


## Testing

- Will be done using PHPUnit