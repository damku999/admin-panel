#!/bin/bash

# Laravel Insurance Management System - Test Execution Script
# This script runs the comprehensive test suite and generates coverage reports

echo "=========================================="
echo "Laravel Insurance Management System Tests"
echo "=========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Create coverage directory
echo -e "${YELLOW}Creating coverage directories...${NC}"
mkdir -p tests/coverage/html
mkdir -p tests/coverage

# Function to run tests with error handling
run_test_suite() {
    local suite_name=$1
    local suite_path=$2

    echo -e "${YELLOW}Running $suite_name tests...${NC}"

    if php artisan test --testsuite=$suite_name --stop-on-failure; then
        echo -e "${GREEN}✅ $suite_name tests passed${NC}"
        return 0
    else
        echo -e "${RED}❌ $suite_name tests failed${NC}"
        return 1
    fi
}

# Function to check coverage thresholds
check_coverage() {
    echo -e "${YELLOW}Checking coverage thresholds...${NC}"

    # Extract coverage percentage from clover.xml if it exists
    if [[ -f "tests/coverage/clover.xml" ]]; then
        # This is a simple check - in production you'd use proper XML parsing
        local coverage=$(grep -o 'statements="[0-9]*"' tests/coverage/clover.xml | head -1 | grep -o '[0-9]*')
        local covered=$(grep -o 'coveredstatements="[0-9]*"' tests/coverage/clover.xml | head -1 | grep -o '[0-9]*')

        if [[ -n "$coverage" && -n "$covered" ]]; then
            local percentage=$((covered * 100 / coverage))
            echo "Code Coverage: ${percentage}%"

            if [[ $percentage -ge 95 ]]; then
                echo -e "${GREEN}✅ Excellent coverage (${percentage}% >= 95%)${NC}"
                return 0
            elif [[ $percentage -ge 80 ]]; then
                echo -e "${YELLOW}⚠️  Good coverage (${percentage}% >= 80%)${NC}"
                return 0
            else
                echo -e "${RED}❌ Insufficient coverage (${percentage}% < 80%)${NC}"
                return 1
            fi
        fi
    fi

    echo -e "${YELLOW}Coverage report not found or unreadable${NC}"
    return 1
}

# Pre-test setup
echo -e "${YELLOW}Setting up test environment...${NC}"

# Clear cache and configs
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Ensure test database is ready
echo -e "${YELLOW}Preparing test database...${NC}"
php artisan migrate:fresh --env=testing --force
php artisan db:seed --env=testing --force

# Run individual test suites
test_results=()

# Run Unit Tests
if run_test_suite "Unit" "Unit"; then
    test_results[0]=1
else
    test_results[0]=0
fi

# Run Feature Tests
if run_test_suite "Feature" "Feature"; then
    test_results[1]=1
else
    test_results[1]=0
fi

# Run Integration Tests (if they exist)
if [[ -d "tests/Integration" ]]; then
    if run_test_suite "Integration" "Integration"; then
        test_results[2]=1
    else
        test_results[2]=0
    fi
fi

# Run Security Tests (if they exist)
if [[ -d "tests/Security" ]]; then
    if run_test_suite "Security" "Security"; then
        test_results[3]=1
    else
        test_results[3]=0
    fi
fi

echo ""
echo "=========================================="
echo "RUNNING FULL TEST SUITE WITH COVERAGE"
echo "=========================================="

# Run all tests with coverage
if php artisan test --coverage --coverage-html=tests/coverage/html --coverage-clover=tests/coverage/clover.xml; then
    echo -e "${GREEN}✅ All tests completed${NC}"

    # Check coverage thresholds
    check_coverage
    coverage_result=$?

else
    echo -e "${RED}❌ Test suite failed${NC}"
    exit 1
fi

echo ""
echo "=========================================="
echo "TEST RESULTS SUMMARY"
echo "=========================================="

# Display results
echo "Unit Tests:        $([ ${test_results[0]} -eq 1 ] && echo -e "${GREEN}PASSED${NC}" || echo -e "${RED}FAILED${NC}")"
echo "Feature Tests:     $([ ${test_results[1]} -eq 1 ] && echo -e "${GREEN}PASSED${NC}" || echo -e "${RED}FAILED${NC}")"

if [[ -d "tests/Integration" ]]; then
    echo "Integration Tests: $([ ${test_results[2]} -eq 1 ] && echo -e "${GREEN}PASSED${NC}" || echo -e "${RED}FAILED${NC}")"
fi

if [[ -d "tests/Security" ]]; then
    echo "Security Tests:    $([ ${test_results[3]} -eq 1 ] && echo -e "${GREEN}PASSED${NC}" || echo -e "${RED}FAILED${NC}")"
fi

echo ""
echo "Coverage Report: tests/coverage/html/index.html"
echo "Coverage Data:   tests/coverage/clover.xml"

# Overall result
failed_count=0
for result in "${test_results[@]}"; do
    if [[ $result -eq 0 ]]; then
        failed_count=$((failed_count + 1))
    fi
done

if [[ $failed_count -eq 0 && $coverage_result -eq 0 ]]; then
    echo -e "${GREEN}"
    echo "🎉 ALL TESTS PASSED WITH SUFFICIENT COVERAGE!"
    echo "✅ Ready for production deployment"
    echo -e "${NC}"
    exit 0
else
    echo -e "${RED}"
    echo "❌ SOME TESTS FAILED OR COVERAGE IS INSUFFICIENT"
    echo "Please review and fix issues before deployment"
    echo -e "${NC}"
    exit 1
fi