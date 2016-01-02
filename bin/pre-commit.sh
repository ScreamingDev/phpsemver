#!/usr/bin/env bash

status=0

bin/phpspec run --config etc/phpspec.yml
status+=$?

bin/phpcs --standard=etc/phpcs.xml lib
status+=$?

bin/phpmd --exclude Test lib text etc/phpmd.xml
status+=$?

bin/phpunit -c etc/phpunit.xml --disallow-test-output --coverage-text lib/Test
status+=$?

exit ${status}