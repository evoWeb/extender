#!/usr/bin/env bash

export PACKAGE="evoWeb/extender";
export T3EXTENSION="extender";

runUnitTests () {
    local PHP=${1};
    local TYPO3_VERSION=${2};
    local TESTING_FRAMEWORK=${3};
    local PHPUNIT_VERSION=${4};
    local UNITTEST_FOLDER=${5};
    local UNITTEST_SUITE=${6};
    local DB_DRIVER=${7};
    local COMPOSER="/usr/local/bin/composer";

    rm -rf .Build/Web
    rm -rf .Build/bin

    ${PHP} --version
    ${PHP} ${COMPOSER} --version

    export TYPO3_PATH_WEB=${PWD}/.Build/Web;
    ${PHP} ${COMPOSER} require -n -q typo3/minimal="${TYPO3_VERSION}";
    if [ ! -z "${PHPUNIT_VERSION}" ]; then ${PHP} ${COMPOSER} require -n -q --dev phpunit/phpunit="${PHPUNIT_VERSION}"; fi;
    if [ ! -z "${TESTING_FRAMEWORK}" ]; then ${PHP} ${COMPOSER} require -n -q --dev typo3/testing-framework="${TESTING_FRAMEWORK}"; fi;
    git checkout composer.json;

    mkdir -p .Build/Web/typo3conf/ext/
    [ -L ".Build/Web/typo3conf/ext/${T3EXTENSION}" ] || ln -snvf ../../../../. ".Build/Web/typo3conf/ext/${T3EXTENSION}"

    echo "Running php lint";
    errors=$(find . -name \*.php ! -path "./.Build/*" -exec ${PHP} -d display_errors=stderr -l {} 2>&1 >/dev/null \;) && echo "$errors" && test -z "$errors"

    echo "Running $TYPO3_VERSION functional tests";
    export typo3DatabaseName="typo3";
    export typo3DatabaseHost="localhost";
    export typo3DatabaseUsername="root";
    export typo3DatabasePassword="";
    export typo3DatabaseDriver="pdo_sqlite";
    php .Build/bin/phpunit --colors -c vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTests.xml Tests/Functional/;

    rm composer.lock
    rm -rf .Build/Web/
    rm -rf .Build/bin/
    rm -rf .Build/vendor/
    rm -rf var/
    rm -rf vendor/
}

cd ../;

runUnitTests "/usr/bin/php7.2" "^10.0.0" "~5.0.11";
runUnitTests "/usr/bin/php7.2" "dev-master as 10.0.0";
