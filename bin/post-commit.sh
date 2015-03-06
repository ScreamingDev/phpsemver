#!/bin/bash
#

## A hook script to verify what is about to be committed.
## Called by "git commit" with no arguments.
## The hook should exit with non-zero status after ...
##
## - Non-ASCII filenames
## - PHPMD (mess detection as defined in etc/phpmd.xml)
##
## Issues:
##
## - Broken test: Whitespace errors
##

#-----------------------------------------------------------------------------
# -x = debugging on (xtrace)
# -h = Remember  the  location of commands as they
#      are  looked  up  for  execution.   This  is
#      enabled by default.
# -p = Turn on privileged mode.  In this mode, the
#      $ENV and $BASH_ENV files are not processed,
#      shell functions are not inherited from  the
#      environment, and the SHELLOPTS variable, if
#      it appears in the environment, is  ignored.
#
# (read 'man set' for full info on options)
set -h -p

#
# Variables:
#

MAJOR_TARGETS[0]="lib"

#-----------------------------------------------------------------------------
# internals:

declare -r Me="${0##*/}"                       # without path
#declare -r Who="${Me#*.}"                     # without extension

declare -r Who=$(basename $Me .sh)             # without extension
#declare -r ConfigFile="/etc/${Who}.conf"      # config file: system
#declare -r ConfigFile="~/${Who}.conf"         # config file: user
declare -r ConfigFile="./${Who}.conf"          # config file: local
declare -r MyFiles="/tmp/${Who}"               # Name for temp files

declare -r MyPID="$$"                          # PID
declare -r GlobalLock="${MyFiles}.MenAtWork"   # LOCK-file
declare -r LocalLock="./${Who}.lock"           # LOCK-file

declare -r help=$(grep "^## " "${BASH_SOURCE[0]}" | cut -c 4-)
declare -r version=$(grep "^#- " "${BASH_SOURCE[0]}" | cut -c 4-)

# logfiles
mkdir -p ./var/log
exec 1>&2 | tee ./var/log/${Who}.log

function MyMain() {
	if [ -f "$LocalLock" ]; then
		echo "Commit already running - don't make me work too hard!"
		exit 1
	fi

	touch "$LocalLock"
	trap 'ret_val=$?; rm -f "$LocalLock"; exit $ret_val' INT TERM EXIT

	# prepare targets
	targets=()
	for target in "${MAJOR_TARGETS[@]}"
	do
		for single in $target; do
			if [ ! -d ${single} ]
			then
				continue;
			fi

			targets+=("${single}")
		done
	done

	# Parse args
	while [ "$1" ]; do
		optarg=""
		opt=""
		case "$1" in
		   -*=*)
			  opt=`echo "$1" | sed 's/=.*//'`
			  optarg=`echo "$1" | sed 's/[-_a-zA-Z0-9]*=//'`
		   ;;
			*)
			  opt=$1
		   ;;
		esac

		case $opt in
			-v)
				opt_v
				exit
			;;
			-h|*)
			  opt_h
			  exit 1
			;;
		esac
	done

    # Target independent tests

	test_phpunit lib/Test

	if [ "${#targets[@]}" -eq "0" ]; then # no target found
		return
	fi

	# Target dependent tests

}

function test_phpunit() {
    assert_exec phpunit ./bin/phpunit -c etc/phpunit.xml --disallow-test-output --coverage-text ${*}
}

function assert_exec() {
    config=$1;
    shift
    output=$(${*});

    if [ "$?" -ne "0" ]; then
        messed_up $config "$output"
    fi
}

function messed_up() {
	echo "$2" | sed -e 's/^[\t]*//'
	echo ""
	echo If you know what you are doing you can disable this check using:
	echo ""
	echo git config hooks.$1 false
	exit 1
}

function join { local IFS="$1"; shift; echo "$*"; }

opt_h() {
	echo "$help"
}

opt_v() {
	echo "$version"
}

MyMain;