#!/usr/bin/bash

TEST_TYPE=$1
if [[ "unit" != $TEST_TYPE && "integration" != $TEST_TYPE ]]; then
    echo 'Error: first parameter of phpunit.sh must be either "unit" or "integration"'
    exit 1
fi

RUN_MODE=$2
if [[ "just-test" != $RUN_MODE && "coverage-report" != $RUN_MODE && "coverage-100" != $RUN_MODE ]]; then
    echo 'Error: first parameter of phpunit.sh must be "just-test", "coverage-report" or "coverage-100"'
    exit 1
fi

COMMAND="vendor/bin/phpunit --fail-on-empty-test-suite --fail-on-warning --fail-on-risky --fail-on-deprecation --fail-on-notice --fail-on-skipped --fail-on-incomplete --display-incomplete --display-skipped --display-deprecations --display-errors --display-notices --display-warnings"

if [[ "just-test" == $RUN_MODE ]]; then
    COMMAND="$COMMAND tests/$TEST_TYPE/"
elif [[ "coverage-report" == $RUN_MODE ]]; then
    COMMAND="$COMMAND --coverage-html tests/$TEST_TYPE/coverage/ --coverage-filter src/ tests/$TEST_TYPE/"
elif [[ "coverage-100" == $RUN_MODE ]]; then
    COMMAND="$COMMAND --coverage-text=.phpunit-temp-coverage tests/$TEST_TYPE/coverage/ --coverage-filter src/ tests/$TEST_TYPE/"
fi

$COMMAND


if [[ "coverage-100" == $RUN_MODE ]]; then
    cat .phpunit-temp-coverage
    echo

    LINES=$(grep Lines .phpunit-temp-coverage | head -n 1)
    COVERAGE=${LINES:11:6}

    rm .phpunit-temp-coverage

    if [[ "100.00" != $COVERAGE ]]; then
        echo "Coverage is $COVERAGE, please reach 100%"
        exit 1
    fi
fi
