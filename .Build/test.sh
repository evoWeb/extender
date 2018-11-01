#!/usr/bin/env bash

export PACKAGE="evoWeb/extender";
export T3EXTENSION="extender";

runFunctionalTests () {
    PHP=${1};
    TYPO3_VERSION=${2};
    TESTING_FRAMEWORK=${3};
    PHPUNIT_VERSION=${4};
    UNITTEST_FOLDER=${5};
    UNITTEST_SUITE=${6};
    DB_DRIVER=${7};
    COMPOSER="/usr/local/bin/composer";

    rm -rf ./*
    rm -rf ./.*

    git clone --depth=50 --branch=develop "https://github.com/$PACKAGE.git" "$PACKAGE"
    cd "$PACKAGE"

    ${PHP} --version
    ${PHP} ${COMPOSER} --version

    export TYPO3_PATH_WEB=$PWD/.Build/Web;
    ${PHP} ${COMPOSER} require typo3/minimal="$TYPO3_VERSION";
    if [ ! -z "$PHPUNIT_VERSION" ]; then ${PHP} ${COMPOSER} require --dev phpunit/phpunit="$PHPUNIT_VERSION"; fi;
    if [ ! -z "$TESTING_FRAMEWORK" ]; then ${PHP} ${COMPOSER} require --dev typo3/testing-framework="$TESTING_FRAMEWORK"; fi;
    git checkout composer.json;

    mkdir -p .Build/Web/typo3conf/ext/
    [ -L ".Build/Web/typo3conf/ext/$T3EXTENSION" ] || ln -snvf ../../../../. ".Build/Web/typo3conf/ext/$T3EXTENSION"

    echo "Running php lint";
    errors=$(find . -name \*.php ! -path "./.Build/*" -exec ${PHP} -d display_errors=stderr -l {} 2>&1 >/dev/null \;) && echo "$errors" && test -z "$errors"

    echo "Running $TYPO3_VERSION unit tests in folder '$UNITTEST_FOLDER' with suite '$UNITTEST_SUITE'";
    .Build/bin/phpunit --colors -c ${UNITTEST_SUITE} ${UNITTEST_FOLDER}

    rm composer.lock
    rm -rf .Build/Web/
    rm -rf .Build/bin/
    rm -rf var/

    cd ../../
}

runFunctionalTests "/usr/bin/php5.6" "^7.6.0" "" "~4.8.0" "Tests/Unit7/" ".Build/Web/typo3/sysext/core/Build/UnitTests.xml" "mysqli";
runFunctionalTests "/usr/bin/php7.2" "^7.6.0" "" "~4.8.0" "Tests/Unit7/" ".Build/Web/typo3/sysext/core/Build/UnitTests.xml" "mysqli";
runFunctionalTests "/usr/bin/php7.0" "^8.7.0" "~1.3.0" "" "Tests/Unit/" ".Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml" "mysqli";
runFunctionalTests "/usr/bin/php7.1" "^8.7.0" "~1.3.0" "" "Tests/Unit/" ".Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml" "mysqli";
runFunctionalTests "/usr/bin/php7.2" "^8.7.0" "~1.3.0" "" "Tests/Unit/" ".Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml" "mysqli";
runFunctionalTests "/usr/bin/php7.2" "^9.5.0" "~4.10.0" "" "Tests/Unit/" ".Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml" "pdo_sqlite";
runFunctionalTests "/usr/bin/php7.2" "dev-master as 10.0.0" "~4.10.0" "" "Tests/Unit/" ".Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml" "pdo_sqlite";
