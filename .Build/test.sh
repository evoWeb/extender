#!/usr/bin/env bash

export PACKAGE="evoWeb/extender";
export T3EXTENSION="extender";

runFunctionalTests () {
    local PHP_VERSION="${1}";
    local TYPO3_VERSION=${2};
    local TESTING_FRAMEWORK=${3};
    local TEST_PATH=${4};
    local PREFER_LOWEST=${5};
    local COMPOSER="/usr/local/bin/composer";

    echo "------"
    echo "Run unit and/or functional tests on TYPO3 ${TYPO3_VERSION} with PHP ${PHP} and testing framework ${TESTING_FRAMEWORK}"
    echo "------"

    ./runTests.sh -p ${PHP_VERSION} -s lintPhp;

    ./runTests.sh -p ${PHP_VERSION} -s composerInstall;

    ./runTests.sh -p ${PHP_VERSION} -s composerInstallPackage -q "typo3/cms-core:${TYPO3_VERSION}";

    ./runTests.sh -p ${PHP_VERSION} -s composerInstallPackage -q "typo3/testing-framework:${TESTING_FRAMEWORK}" -o " --dev ${PREFER_LOWEST}";

    ./runTests.sh -p ${PHP_VERSION} -s composerValidate;

    ./runTests.sh -p ${PHP_VERSION} -x -s functional ${TEST_PATH};
}

#runFunctionalTests "7.4" "^11.0.0" "^6.6.2" "Tests/Functional";
runFunctionalTests "8.1" "^12.0.0" "dev-main" "Tests/Functional12";
#runFunctionalTests "8.1" "^12.0.0" "dev-main" "Tests/Functional12" "--prefer-lowest";
#runFunctionalTests "8.1" "dev-main" "dev-main" "Tests/Functional12";

exit 0;

./runTests.sh -s cleanTests;
git checkout ../composer.json;
