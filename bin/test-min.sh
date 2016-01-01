#!/usr/bin/env bash

testCode=0
bin/pre-commit.sh
testCode+=$?

exit ${testCode}