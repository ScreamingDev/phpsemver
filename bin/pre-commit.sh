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

MAJOR_TARGETS[0]="lib/PHPSemVer"

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

	test_non-ascii

	if [ "${#targets[@]}" -eq "0" ]; then # no target found
		return
	fi

	assert_exec phpspec ./bin/phpspec run --config etc/phpspec.yml

	test_phpmd "${targets[@]}"
}

function test_non-ascii() {
	# Initial commit: diff against an empty tree object
	against=$(git hash-object -t tree /dev/null)

	# Or against last commit, if given
	if git rev-parse --verify HEAD >/dev/null 2>&1
	then
		against=HEAD
	fi

	# If you want to allow non-ASCII filenames set this variable to true.
	allownonascii=$(git config --bool hooks.allownonascii)

	# Cross platform projects tend to avoid non-ASCII filenames; prevent
	# them from being added to the repository. We exploit the fact that the
	# printable range starts at the space character and ends with tilde.
	if [ "$allownonascii" != "true" ] &&
		# Note that the use of brackets around a tr range is ok here, (it's
		# even required, for portability to Solaris 10's /usr/bin/tr), since
		# the square bracket bytes happen to fall in the designated range.
		test $(git diff --cached --name-only --diff-filter=A -z $against |
		  LC_ALL=C tr -d '[ -~]\0' | wc -c) != 0
	then
		messed_up allownonascii "
		Attempt to add a non-ASCII file name.
		This can cause problems if you want to work with people on other platforms.
		To be portable it is advisable to rename the file.
		"
	fi
}
# If there are whitespace errors, print the offending file names and fail.
# exec git diff-index --check --cached $against --

# PHP Mess Detector

function test_phpmd() {
	ignoremess=$(git config --bool hooks.phpmd)

	if [ "$ignoremess" == "false" ]; then
		return;
	fi

	phpmd_attribute=$(join , "$@")

	output=$(./bin/phpmd ${phpmd_attribute} text etc/phpmd.xml --exclude vendor,includes/lib)

	if [ "$?" -eq "0" ]; then
		return;
	fi

	messed_up phpmd "
	Attempt to add bad style of code.
	This can cause problems if you want to debug or enhance the functionality.
	To be maintainable and reusable it is advisable to refactor the code.

	$output
	"
}

function assert_exec() {
    config=$1;
    shift
    echo "Asserting $config ...";
    echo "";
    ${*};

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
	exit 2
}

function join { local IFS="$1"; shift; echo "$*"; }

opt_h() {
	echo "$help"
}

opt_v() {
	echo "$version"
}

MyMain;