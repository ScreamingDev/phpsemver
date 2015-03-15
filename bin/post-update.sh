#!/bin/bash

[[ ! -L .git/hooks/post-update ]] || rm .git/hooks/post-update
[[ ! -L .git/hooks/pre-commit ]] || rm .git/hooks/pre-commit
[[ ! -L .git/hooks/post-commit ]] || rm .git/hooks/post-commit

[[ -f .git/hooks/post-update ]] || ln -s ../../bin/post-update.sh .git/hooks/post-update
[[ -f .git/hooks/pre-commit ]] || ln -s ../../bin/pre-commit.sh .git/hooks/pre-commit

# assert composer
if [[ ! -f ./bin/composer ]]; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar bin/composer
fi

# assert the usr dir
if [[ ! -d ./usr ]]; then
    mkdir usr
fi

git config --remove-section hooks 2>/dev/null

./bin/composer self-update
./bin/composer install
