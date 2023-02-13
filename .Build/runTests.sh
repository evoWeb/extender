#!/usr/bin/env bash

# ! COPIED from https://github.com/TYPO3/typo3/blob/main/Build/Scripts/runTests.sh

#
# TYPO3 core test runner based on docker and docker-compose.
#

# Function to write a .env file in Build/testing-docker/local
# This is read by docker-compose and vars defined here are
# used in Build/testing-docker/local/docker-compose.yml
setUpDockerComposeDotEnv() {
    # Delete possibly existing local .env file if exists
    [ -e .env ] && rm .env
    # Set up a new .env file for docker-compose
    {
        echo "COMPOSE_PROJECT_NAME=local"
        # To prevent access rights of files created by the testing, the docker image later
        # runs with the same user that is currently executing the script. docker-compose can't
        # use $UID directly itself since it is a shell variable and not an env variable, so
        # we have to set it explicitly here.
        echo "HOST_UID=$(id -u)"
        # Your local user
        echo "CORE_ROOT=${CORE_ROOT}"
        echo "HOST_USER=${USER}"
        echo "TEST_FILE=${TEST_FILE}"
        echo "PHP_XDEBUG_ON=${PHP_XDEBUG_ON}"
        echo "PHP_XDEBUG_PORT=${PHP_XDEBUG_PORT}"
        echo "DOCKER_PHP_IMAGE=${DOCKER_PHP_IMAGE}"
        echo "EXTRA_TEST_OPTIONS=${EXTRA_TEST_OPTIONS}"
        echo "SCRIPT_VERBOSE=${SCRIPT_VERBOSE}"
        echo "PHPUNIT_RANDOM=${PHPUNIT_RANDOM}"
        echo "CGLCHECK_DRY_RUN=${CGLCHECK_DRY_RUN}"
        echo "DATABASE_DRIVER=${DATABASE_DRIVER}"
        echo "MARIADB_VERSION=${MARIADB_VERSION}"
        echo "MYSQL_VERSION=${MYSQL_VERSION}"
        echo "POSTGRES_VERSION=${POSTGRES_VERSION}"
        echo "PHP_VERSION=${PHP_VERSION}"
        echo "CHUNKS=${CHUNKS}"
        echo "DOCKER_SELENIUM_IMAGE=${DOCKER_SELENIUM_IMAGE}"
        echo "IS_CORE_CI=${IS_CORE_CI}"
        echo "PHPSTAN_CONFIG_FILE=${PHPSTAN_CONFIG_FILE}"
        echo "PACKAGE=${PACKAGE}"
        echo "COMPOSER_PARAMETER=${COMPOSER_PARAMETER}"
    } > .env
}

# Options -a and -d depend on each other. The function
# validates input combinations and sets defaults.
handleDbmsAndDriverOptions() {
    case ${DBMS} in
        sqlite)
            if [ -n "${DATABASE_DRIVER}" ]; then
                echo "Invalid option -a ${DATABASE_DRIVER} with -d ${DBMS}" >&2
                echo >&2
                echo "call \".Build/Scripts/runTests.sh -h\" to display help and valid options" >&2
                exit 1
            fi
            ;;
    esac
}

cleanCacheFiles() {
    # > caches
    echo -n "Clean caches ... " ; \
    rm -rf \
        ./.cache ; \
        echo "done"
}

cleanTestFiles() {
    # > composer distribution test
    echo -n "Clean composer distribution test ... " ; \
    rm -rf \
        ./.cache \
        ../composer.lock ; \
       echo "done"

    # > test related
    echo -n "Clean test related files ... " ; \
    rm -rf \
        ./.env \
        ./typo3temp \
        ./Web ; \
        echo "done"
}

cleanRenderedDocumentationFiles() {
    # > caches
    echo -n "Clean rendered documentation files ... " ; rm -rf \
        ../*/Documentation-GENERATED-temp ; \
        echo "done"
}

# Load help text into $HELP
# @todo Remove xdebug / php8.2 note after PHP8.2 image contains working xdebug.
read -r -d '' HELP <<EOF
TYPO3 core test runner. Execute acceptance, unit, functional and other test suites in
a docker based test environment. Handles execution of single test files, sending
xdebug information to a local IDE and more.
Recommended docker version is >=20.10 for xdebug break pointing to work reliably, and
a recent docker-compose (tested >=1.21.2) is needed.
Usage: $0 [options] [file]
No arguments: Run all unit tests with PHP 8.1
Options:
    -s <...>
        Specifies which test suite to run
            - acceptance: main application acceptance tests
            - acceptanceInstall: installation acceptance tests, only with -d mariadb|postgres|sqlite
            - buildCss: execute scss to css builder
            - buildJavascript: execute typescript to javascript builder
            - cgl: test and fix all core php files
            - cglGit: test and fix latest committed patch for CGL compliance
            - cglHeader: test and fix file header for all core php files
            - cglHeaderGit: test and fix latest committed patch for CGL file header compliance
            - checkAnnotations: check php code for allowed annotations
            - checkBom: check UTF-8 files do not contain BOM
            - checkComposer: check composer.json files for version integrity
            - checkExceptionCodes: test core for duplicate exception codes
            - checkExtensionScannerRst: test all .rst files referenced by extension scanner exist
            - checkFilePathLength: test core file paths do not exceed maximum length
            - checkGitSubmodule: test core git has no sub modules defined
            - checkGruntClean: Verify "grunt build" is clean. Warning: Executes git commands! Usually used in CI only.
            - checkNamespaceIntegrity: Verify namespace integrity in class and test code files are in good shape.
            - checkPermissions: test some core files for correct executable bits
            - checkRst: test .rst files for integrity
            - checkTestMethodsPrefix: check tests methods do not start with "test"
            - clean: clean up build, cache and testing related files and folders
            - cleanBuild: clean up build related files and folders
            - cleanCache: clean up cache related files and folders
            - cleanRenderedDocumentation: clean up rendered documentation files and folders (Documentation-GENERATED-temp)
            - cleanTests: clean up test related files and folders
            - composerInstall: "composer install"
            - composerInstallMax: "composer update", with no platform.php config.
            - composerInstallMin: "composer update --prefer-lowest", with platform.php set to PHP version x.x.0.
            - composerTestDistribution: "composer update" in Build/composer to verify core dependencies
            - composerValidate: "composer validate"
            - functional: PHP functional tests
            - functionalDeprecated: deprecated PHP functional tests
            - lintPhp: PHP linting
            - lintScss: SCSS linting
            - lintTypescript: TS linting
            - lintHtml: HTML linting
            - listExceptionCodes: list core exception codes in JSON format
            - phpstan: phpstan tests
            - phpstanGenerateBaseline: regenerate phpstan baseline, handy after phpstan updates
            - unit (default): PHP unit tests
            - unitDeprecated: deprecated PHP unit tests
            - unitJavascript: JavaScript unit tests
            - unitRandom: PHP unit tests in random order, add -o <number> to use specific seed
    -a <mysqli|pdo_mysql>
        Only with -s functional|functionalDeprecated
        Specifies to use another driver, following combinations are available:
            - mysql
                - mysqli (default)
                - pdo_mysql
            - mariadb
                - mysqli (default)
                - pdo_mysql
    -d <sqlite|mariadb|mysql|postgres>
        Only with -s functional|functionalDeprecated|acceptance|acceptanceInstall
        Specifies on which DBMS tests are performed
            - sqlite: (default): use sqlite
            - mariadb use mariadb
            - mysql: use MySQL server
            - postgres: use postgres
    -i <10.3|10.4|10.5|10.6|10.7|10.8|10.9|10.10>
        Only with -d mariadb
        Specifies on which version of mariadb tests are performed
            - 10.3 (default)
            - 10.4
            - 10.5
            - 10.6
            - 10.7
            - 10.8
            - 10.9
            - 10.10
    -j <8.0>
        Only with -d mysql
        Specifies on which version of mysql tests are performed
            - 8.0 (default)
    -k <10|11|12|13|14|15>
        Only with -d postgres
        Specifies on which version of postgres tests are performed
            - 10 (default)
            - 11
            - 12
            - 13
            - 14
            - 15
    -c <chunk/numberOfChunks>
        Only with -s functional|acceptance
        Hack functional or acceptance tests into #numberOfChunks pieces and run tests of #chunk.
        Example -c 3/13
    -p <7.4|8.1|8.2>
        Specifies the PHP minor version to be used
            - 8.1 (default): use PHP 8.1
            - 8.2: use PHP 8.2 (note that xdebug is currently not available for PHP8.2)
    -e "<phpunit options>"
        Only with -s functional|functionalDeprecated|unit|unitDeprecated|unitRandom|acceptance
        Additional options to send to phpunit (unit & functional tests) or codeception (acceptance
        tests). For phpunit, options starting with "--" must be added after options starting with "-".
        Example -e "-v --filter canRetrieveValueWithGP" to enable verbose output AND filter tests
        named "canRetrieveValueWithGP"
    -x
        Only with -s functional|functionalDeprecated|unit|unitDeprecated|unitRandom|acceptance|acceptanceInstall
        Send information to host instance for test or system under test break points. This is especially
        useful if a local PhpStorm instance is listening on default xdebug port 9003. A different port
        can be selected with -y
    -y <port>
        Send xdebug information to a different port than default 9003 if an IDE like PhpStorm
        is not listening on default port.
    -o <number>
        Only with -s unitRandom
        Set specific random seed to replay a random run in this order again. The phpunit randomizer
        outputs the used seed at the end (in gitlab core testing logs, too). Use that number to
        replay the unit tests in that order.
    -n
        Only with -s cgl|cglGit|cglHeader|cglGitHeader
        Activate dry-run in CGL check that does not actively change files and only prints broken ones.
    -u
        Update existing typo3/core-testing-*:latest docker images and remove dangling local docker volumes.
        Maintenance call to docker pull latest versions of the main php images. The images are updated once
        in a while and only the latest ones are supported by core testing. Use this if weird test errors occur.
        Also removes obsolete image versions of typo3/core-testing-*.
    -v
        Enable verbose script output. Shows variables and docker commands.
    -h
        Show this help.
Examples:
    # Run all core unit tests using PHP 8.1
    ./Build/Scripts/runTests.sh
    ./Build/Scripts/runTests.sh -s unit
    # Run all core units tests and enable xdebug (have a PhpStorm listening on port 9003!)
    ./Build/Scripts/runTests.sh -x
    # Run unit tests in phpunit verbose mode with xdebug on PHP 8.1 and filter for test canRetrieveValueWithGP
    ./Build/Scripts/runTests.sh -x -p 8.1 -e "-v --filter canRetrieveValueWithGP"
    # Run functional tests in phpunit with a filtered test method name in a specified file
    # example will currently execute two tests, both of which start with the search term
    ./Build/Scripts/runTests.sh -s functional -e "--filter deleteContent" typo3/sysext/core/Tests/Functional/DataHandling/Regular/Modify/ActionTest.php
    # Run unit tests with PHP 8.1 and have xdebug enabled
    ./Build/Scripts/runTests.sh -x -p 8.1
    # Run functional tests on postgres with xdebug, php 8.1 and execute a restricted set of tests
    ./Build/Scripts/runTests.sh -x -p 8.1 -s functional -d postgres typo3/sysext/core/Tests/Functional/Authentication
    # Run functional tests on mariadb 10.5
    ./Build/Scripts/runTests.sh -d mariadb -i 10.5
    # Run functional tests on postgres 11
    ./Build/Scripts/runTests.sh -s functional -d postgres -k 11
    # Run restricted set of application acceptance tests
    ./Build/Scripts/runTests.sh -s acceptance typo3/sysext/core/Tests/Acceptance/Application/Login/BackendLoginCest.php:loginButtonMouseOver
    # Run installer tests of a new instance on sqlite
    ./Build/Scripts/runTests.sh -s acceptanceInstall -d sqlite
EOF

# Go to the directory this script is located, so everything else is relative
# to this dir, no matter from where this script is called.
THIS_SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null && pwd)"
cd "$THIS_SCRIPT_DIR" || exit 1

# Set core root path by checking whether realpath exists
if ! command -v realpath &> /dev/null; then
    echo "Consider installing realpath for properly resolving symlinks" >&2
    CORE_ROOT="${PWD}/../"
else
    CORE_ROOT=$(realpath "${PWD}/../")
fi

# Option defaults
TEST_SUITE="unit"
DBMS="sqlite"
PHP_VERSION="8.1"
PHP_XDEBUG_ON=0
PHP_XDEBUG_PORT=9003
EXTRA_TEST_OPTIONS=""
SCRIPT_VERBOSE=0
PHPUNIT_RANDOM=""
CGLCHECK_DRY_RUN=""
DATABASE_DRIVER=""
MARIADB_VERSION="10.3"
MYSQL_VERSION="8.0"
POSTGRES_VERSION="10"
CHUNKS=0
DOCKER_SELENIUM_IMAGE="selenium/standalone-chrome:4.0.0-20211102"
IS_CORE_CI=0
PHPSTAN_CONFIG_FILE="phpstan.local.neon"
PACKAGE=""
COMPOSER_PARAMETER=""

# ENV var "CI" is set by gitlab-ci. We use it here to distinct 'local' and 'CI' environment.
if [ "$CI" == "true" ]; then
    IS_CORE_CI=1
    PHPSTAN_CONFIG_FILE="phpstan.ci.neon"
fi

# Detect arm64 and use a seleniarm image.
# In a perfect world selenium would have a arm64 integrated, but that is not on the horizon.
# So for the time being we have to use seleniarm image.
ARCH=$(uname -m)
if [ $ARCH = "arm64" ]; then
    DOCKER_SELENIUM_IMAGE="seleniarm/standalone-chromium:4.1.2-20220227"
    echo "Architecture" $ARCH "requires" $DOCKER_SELENIUM_IMAGE "to run acceptance tests."
fi

# Option parsing
# Reset in case getopts has been used previously in the shell
OPTIND=1
# Array for invalid options
INVALID_OPTIONS=();
# Simple option parsing based on getopts (! not getopt)
while getopts ":a:s:c:d:i:j:k:p:q:e:xy:o:nhuv" OPT; do
    case ${OPT} in
        s)
            TEST_SUITE=${OPTARG}
            ;;
        o)
            COMPOSER_PARAMETER=${OPTARG}
            ;;
        p)
            PHP_VERSION=${OPTARG}
            if ! [[ ${PHP_VERSION} =~ ^(7.4|8.1|8.2)$ ]]; then
                INVALID_OPTIONS+=("${OPTARG}")
            fi
            ;;
        q)
            PACKAGE=${OPTARG}
            ;;
        x)
            PHP_XDEBUG_ON=1
            ;;
        y)
            PHP_XDEBUG_PORT=${OPTARG}
            ;;
        h)
            echo "${HELP}"
            exit 0
            ;;
        u)
            TEST_SUITE=update
            ;;
        v)
            SCRIPT_VERBOSE=1
            ;;
        \?)
            INVALID_OPTIONS+=("${OPTARG}")
            ;;
        :)
            INVALID_OPTIONS+=("${OPTARG}")
            ;;
    esac
done

# Exit on invalid options
if [ ${#INVALID_OPTIONS[@]} -ne 0 ]; then
    echo "Invalid option(s):" >&2
    for I in "${INVALID_OPTIONS[@]}"; do
        echo "-"${I} >&2
    done
    echo >&2
    echo "call \".Build/Scripts/runTests.sh -h\" to display help and valid options"
    exit 1
fi

# Move "7.4" to "php74", the latter is the docker container name
DOCKER_PHP_IMAGE=$(echo "php${PHP_VERSION}" | sed -e 's/\.//')

# Set $1 to first mass argument, this is the optional test file or test directory to execute
shift $((OPTIND - 1))
TEST_FILE=${1}

if [ ${SCRIPT_VERBOSE} -eq 1 ]; then
    set -x
fi

# Suite execution
case ${TEST_SUITE} in
    checkComposer)
        setUpDockerComposeDotEnv
        docker-compose run check_composer
        SUITE_EXIT_CODE=$?
        docker-compose down
        ;;
    checkRst)
        setUpDockerComposeDotEnv
        docker-compose run check_rst
        SUITE_EXIT_CODE=$?
        docker-compose down
        ;;
    cleanTests)
        cleanTestFiles
        ;;
    composerInstall)
        setUpDockerComposeDotEnv
        docker-compose run composer_install
        SUITE_EXIT_CODE=$?
        docker-compose down
        ;;
    composerInstallPackage)
        setUpDockerComposeDotEnv
        docker-compose run composer_require_package
        SUITE_EXIT_CODE=$?
        docker-compose down
        ;;
    composerValidate)
        setUpDockerComposeDotEnv
        docker-compose run composer_validate
        SUITE_EXIT_CODE=$?
        docker-compose down
        ;;
    functional)
        handleDbmsAndDriverOptions
        setUpDockerComposeDotEnv
        if [ "${CHUNKS}" -gt 0 ]; then
            docker-compose run functional_split
        fi
        # sqlite has a tmpfs as typo3temp/var/tests/functional-sqlite-dbs/
        # Since docker is executed as root (yay!), the path to this dir is owned by
        # root if docker creates it. Thank you, docker. We create the path beforehand
        # to avoid permission issues on host filesystem after execution.
        mkdir -p "${CORE_ROOT}/.Build/Web/typo3temp/var/tests/functional-sqlite-dbs/"
        docker-compose run prepare_functional_sqlite
        docker-compose run functional_sqlite
        SUITE_EXIT_CODE=$?
        docker-compose down
        ;;
    lintPhp)
        setUpDockerComposeDotEnv
        docker-compose run lint_php
        SUITE_EXIT_CODE=$?
        docker-compose down
        ;;
    *)
        echo "Invalid -s option argument ${TEST_SUITE}" >&2
        echo >&2
        echo "${HELP}" >&2
        exit 1
esac

case ${DBMS} in
    mariadb)
        DBMS_OUTPUT="DBMS: ${DBMS}  version ${MARIADB_VERSION}  driver ${DATABASE_DRIVER}"
        ;;
    mysql)
        DBMS_OUTPUT="DBMS: ${DBMS}  version ${MYSQL_VERSION}  driver ${DATABASE_DRIVER}"
        ;;
    postgres)
        DBMS_OUTPUT="DBMS: ${DBMS}  version ${POSTGRES_VERSION}"
        ;;
    sqlite)
        DBMS_OUTPUT="DBMS: ${DBMS}"
        ;;
    *)
        DBMS_OUTPUT="DBMS not recognized: $DBMS"
        exit 1
esac

# Print summary
if [ ${SCRIPT_VERBOSE} -eq 1 ]; then
    # Turn off verbose mode for the script summary
    set +x
fi
echo "" >&2
echo "###########################################################################" >&2
echo "Result of ${TEST_SUITE}" >&2
if [[ ${IS_CORE_CI} -eq 1 ]]; then
    echo "Environment: CI" >&2
else
    echo "Environment: local" >&2
fi
echo "PHP: ${PHP_VERSION}" >&2
if [[ ${TEST_SUITE} =~ ^(functional|acceptance|acceptanceInstall)$ ]]; then
    echo "${DBMS_OUTPUT}" >&2
fi

if [[ ${SUITE_EXIT_CODE} -eq 0 ]]; then
    echo "SUCCESS" >&2
else
    echo "FAILURE" >&2
fi
echo "###########################################################################" >&2
echo "" >&2

# Exit with code of test suite - This script return non-zero if the executed test failed.
exit $SUITE_EXIT_CODE
