#!/usr/bin/env bash

testCode=0

./bin/test-min.sh
testCode+=$?

exit ${testCode}