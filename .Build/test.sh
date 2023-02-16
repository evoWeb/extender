#!/usr/bin/env bash

export PACKAGE="evoWeb/extender";
export T3EXTENSION="extender";
export NC='\e[0m';
export RED='\e[0;31m';
export GREEN='\e[0;32m';

runLint () {
    local PHP_VERSION="${1}";

    echo "------"
    echo "Run lint with PHP ${PHP_VERSION}"
    echo "------"

    ./runTests.sh -p ${PHP_VERSION} -s lintPhp;
    LINT_EXIT_CODE=$?

    echo "------"
    echo "Finish lint with PHP ${PHP_VERSION}"
    if [[ ${LINT_EXIT_CODE} -eq 0 ]]; then
        echo -e "${GREEN}SUCCESS${NC}"
    else
        echo -e "${RED}FAILURE${NC}"
    fi
    echo "------"
    echo ""
}

runFunctionalTests () {
    local PHP_VERSION="${1}";
    local TYPO3_VERSION=${2};
    local TESTING_FRAMEWORK=${3};
    local TEST_PATH=${4};
    local PREFER_LOWEST=${5};
    local COMPOSER="/usr/local/bin/composer";

    echo "------"
    echo "Run unit and/or functional tests on TYPO3 ${TYPO3_VERSION} with PHP ${PHP_VERSION} and testing framework ${TESTING_FRAMEWORK}"
    echo "------"

    git checkout ../composer.json;

    ./runTests.sh -s cleanTests;

    ./runTests.sh -p ${PHP_VERSION} -s composerInstall;

    ./runTests.sh -p ${PHP_VERSION} -s composerInstallPackage -q "typo3/cms-core:${TYPO3_VERSION}";

    ./runTests.sh -p ${PHP_VERSION} -s composerInstallPackage -q "typo3/testing-framework:${TESTING_FRAMEWORK}" -o " --dev ${PREFER_LOWEST}";

    ./runTests.sh -p ${PHP_VERSION} -s composerValidate;

    ./runTests.sh -p ${PHP_VERSION} -s functional ${TEST_PATH};
    FUNCTIONAL_EXIT_CODE=$?

    echo "------"
    echo "Finish tests on TYPO3 ${TYPO3_VERSION} with PHP ${PHP_VERSION} and testing framework ${TESTING_FRAMEWORK}"
    if [[ ${FUNCTIONAL_EXIT_CODE} -eq 0 ]]; then
        echo -e "${GREEN}SUCCESS${NC}"
    else
        echo -e "${RED}FAILURE${NC}"
    fi
    echo "------"
    echo ""
}

runLint "7.4";
runLint "8.1";

runFunctionalTests "7.4" "^11.0" "^6.6.2" "Tests/Functional";
runFunctionalTests "8.1" "^12.0" "dev-main" "Tests/Functional12";
runFunctionalTests "8.1" "^12.0" "dev-main" "Tests/Functional12" "--prefer-lowest";
runFunctionalTests "8.1" "dev-main" "dev-main" "Tests/Functional12";

./runTests.sh -s clean;
git checkout ../composer.json;
