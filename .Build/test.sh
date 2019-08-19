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

    echo "Running ${TYPO3_VERSION} unit tests in folder '${UNITTEST_FOLDER}' with suite '${UNITTEST_SUITE}'";
    .Build/bin/phpunit --colors -c ${UNITTEST_SUITE} ${UNITTEST_FOLDER}

    rm composer.lock
    rm -rf .Build/Web/
    rm -rf .Build/bin/
    rm -rf .Build/vendor/
    rm -rf var/
}

cd ../;

runUnitTests "/usr/bin/php7.2" "^10.0.0" "~5.0.11" "" "Tests/Unit/" ".Build/Web/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml" "pdo_sqlite";
runUnitTests "/usr/bin/php7.2" "dev-master as 10.0.0" "~5.0.11" "" "Tests/Unit/" ".Build/Web/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml" "pdo_sqlite";
