#!/bin/bash

cp -av ./bin/post-update.sh .git/hooks/post-update
cp -av ./bin/pre-commit.sh .git/hooks/pre-commit

# assert composer
if [[ ! -f ./bin/composer ]]; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar bin/composer
fi

# assert the usr dir
if [[ ! -d ./usr ]]; then
    mkdir usr
fi

./bin/composer self-update
./bin/composer install
