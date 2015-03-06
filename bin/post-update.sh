#!/bin/bash


[[ -f .git/hooks/post-update ]] || ln -s ../../bin/post-update.sh .git/hooks/post-update
[[ -f .git/hooks/pre-commit ]] || ln -s ../../bin/pre-commit.sh .git/hooks/pre-commit
[[ -f .git/hooks/post-commit ]] || ln -s ../../bin/post-commit.sh .git/hooks/post-commit

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
