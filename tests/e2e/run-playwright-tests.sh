#!/bin/bash

# Comprehensive Playwright Test Runner for Insurance Management System
# This script provides a complete testing workflow with various options

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default configuration
ENVIRONMENT="testing"
BROWSERS="chromium"
WORKERS=1
HEADED=false
DEBUG=false
REPORT=true
TIMEOUT=60000
RETRIES=0

# Test categories
SETUP_TESTS=true
ADMIN_TESTS=true
CUSTOMER_TESTS=true
VISUAL_TESTS=false
A11Y_TESTS=false
E2E_TESTS=false
ALL_TESTS=false

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to show usage
show_usage() {
    cat << EOF
🎭 Playwright Test Runner for Insurance Management System

Usage: $0 [OPTIONS]

Test Categories:
  --setup-only          Run only setup tests
  --admin-only          Run only admin interface tests
  --customer-only       Run only customer portal tests
  --visual-only         Run only visual regression tests
  --a11y-only          Run only accessibility tests
  --e2e-only           Run only end-to-end workflow tests
  --all                Run all test categories

Browser Options:
  --chrome             Run tests in Chrome
  --firefox            Run tests in Firefox
  --safari             Run tests in Safari
  --edge               Run tests in Edge
  --all-browsers       Run tests in all browsers

Execution Options:
  --headed             Run tests in headed mode (visible browser)
  --debug              Run tests in debug mode
  --workers=N          Number of parallel workers (default: 1)
  --timeout=N          Test timeout in milliseconds (default: 60000)
  --retries=N          Number of retries on failure (default: 0)

Output Options:
  --no-report          Don't generate HTML report
  --json-report        Generate JSON report
  --junit-report       Generate JUnit XML report

Environment:
  --env=ENV            Environment to test against (default: testing)

Examples:
  $0 --admin-only --chrome --headed
  $0 --all --all-browsers --workers=4
  $0 --visual-only --no-report
  $0 --e2e-only --debug --headed
  $0 --setup-only --env=staging

EOF
}

# Parse command line arguments
parse_args() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            --help|-h)
                show_usage
                exit 0
                ;;
            --setup-only)
                SETUP_TESTS=true
                ADMIN_TESTS=false
                CUSTOMER_TESTS=false
                VISUAL_TESTS=false
                A11Y_TESTS=false
                E2E_TESTS=false
                shift
                ;;
            --admin-only)
                SETUP_TESTS=false
                ADMIN_TESTS=true
                CUSTOMER_TESTS=false
                VISUAL_TESTS=false
                A11Y_TESTS=false
                E2E_TESTS=false
                shift
                ;;
            --customer-only)
                SETUP_TESTS=false
                ADMIN_TESTS=false
                CUSTOMER_TESTS=true
                VISUAL_TESTS=false
                A11Y_TESTS=false
                E2E_TESTS=false
                shift
                ;;
            --visual-only)
                SETUP_TESTS=false
                ADMIN_TESTS=false
                CUSTOMER_TESTS=false
                VISUAL_TESTS=true
                A11Y_TESTS=false
                E2E_TESTS=false
                shift
                ;;
            --a11y-only)
                SETUP_TESTS=false
                ADMIN_TESTS=false
                CUSTOMER_TESTS=false
                VISUAL_TESTS=false
                A11Y_TESTS=true
                E2E_TESTS=false
                shift
                ;;
            --e2e-only)
                SETUP_TESTS=false
                ADMIN_TESTS=false
                CUSTOMER_TESTS=false
                VISUAL_TESTS=false
                A11Y_TESTS=false
                E2E_TESTS=true
                shift
                ;;
            --all)
                ALL_TESTS=true
                shift
                ;;
            --chrome)
                BROWSERS="chromium"
                shift
                ;;
            --firefox)
                BROWSERS="firefox"
                shift
                ;;
            --safari)
                BROWSERS="webkit"
                shift
                ;;
            --edge)
                BROWSERS="msedge"
                shift
                ;;
            --all-browsers)
                BROWSERS="chromium,firefox,webkit"
                shift
                ;;
            --headed)
                HEADED=true
                shift
                ;;
            --debug)
                DEBUG=true
                HEADED=true
                WORKERS=1
                shift
                ;;
            --workers=*)
                WORKERS="${1#*=}"
                shift
                ;;
            --timeout=*)
                TIMEOUT="${1#*=}"
                shift
                ;;
            --retries=*)
                RETRIES="${1#*=}"
                shift
                ;;
            --no-report)
                REPORT=false
                shift
                ;;
            --json-report)
                JSON_REPORT=true
                shift
                ;;
            --junit-report)
                JUNIT_REPORT=true
                shift
                ;;
            --env=*)
                ENVIRONMENT="${1#*=}"
                shift
                ;;
            *)
                print_error "Unknown option: $1"
                show_usage
                exit 1
                ;;
        esac
    done
}

# Function to check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."

    # Check if we're in the right directory
    if [[ ! -f "playwright.config.js" ]]; then
        print_error "playwright.config.js not found. Make sure you're in the project root directory."
        exit 1
    fi

    # Check if Laravel is available
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed or not in PATH"
        exit 1
    fi

    if [[ ! -f "artisan" ]]; then
        print_error "Laravel artisan not found. Make sure you're in the Laravel project root."
        exit 1
    fi

    # Check if Node.js and npm are available
    if ! command -v npm &> /dev/null; then
        print_error "npm is not installed or not in PATH"
        exit 1
    fi

    # Check if Playwright is installed
    if ! npm list @playwright/test &> /dev/null; then
        print_error "Playwright is not installed. Run 'npm install' first."
        exit 1
    fi

    print_success "Prerequisites check passed"
}

# Function to setup test environment
setup_environment() {
    print_status "Setting up test environment..."

    # Ensure .env.testing exists
    if [[ ! -f ".env.testing" ]]; then
        print_warning ".env.testing not found. Creating from .env.example"
        if [[ -f ".env.example" ]]; then
            cp .env.example .env.testing
        else
            print_error "No .env.example found to create .env.testing"
            exit 1
        fi
    fi

    # Set APP_ENV in .env.testing
    if grep -q "APP_ENV=" .env.testing; then
        sed -i.bak "s/APP_ENV=.*/APP_ENV=${ENVIRONMENT}/" .env.testing
    else
        echo "APP_ENV=${ENVIRONMENT}" >> .env.testing
    fi

    # Ensure test database is configured
    if ! grep -q "DB_DATABASE.*test" .env.testing; then
        print_warning "Test database not configured in .env.testing"
    fi

    print_success "Test environment setup complete"
}

# Function to build test command
build_test_command() {
    local cmd="npx playwright test"

    # Add browser projects
    for browser in $(echo $BROWSERS | tr ',' ' '); do
        cmd="$cmd --project=$browser"
    done

    # Add execution options
    if [[ $HEADED == true ]]; then
        cmd="$cmd --headed"
    fi

    if [[ $DEBUG == true ]]; then
        cmd="$cmd --debug"
    fi

    cmd="$cmd --workers=$WORKERS"
    cmd="$cmd --timeout=$TIMEOUT"
    cmd="$cmd --retries=$RETRIES"

    # Add reporter options
    if [[ $REPORT == true ]]; then
        cmd="$cmd --reporter=html"
    else
        cmd="$cmd --reporter=list"
    fi

    echo "$cmd"
}

# Function to run specific test categories
run_tests() {
    local base_cmd=$(build_test_command)

    print_status "Starting Playwright tests..."
    echo "Configuration:"
    echo "  Environment: $ENVIRONMENT"
    echo "  Browsers: $BROWSERS"
    echo "  Workers: $WORKERS"
    echo "  Headed: $HEADED"
    echo "  Debug: $DEBUG"
    echo "  Timeout: $TIMEOUT ms"
    echo "  Retries: $RETRIES"
    echo ""

    # Run setup tests first if needed
    if [[ $SETUP_TESTS == true ]] || [[ $ALL_TESTS == true ]]; then
        print_status "Running setup tests..."
        if $base_cmd tests/e2e/playwright/setup.spec.js; then
            print_success "Setup tests passed"
        else
            print_error "Setup tests failed"
            exit 1
        fi
    fi

    # Run admin tests
    if [[ $ADMIN_TESTS == true ]] || [[ $ALL_TESTS == true ]]; then
        print_status "Running admin interface tests..."
        $base_cmd tests/e2e/playwright/admin/
    fi

    # Run customer tests
    if [[ $CUSTOMER_TESTS == true ]] || [[ $ALL_TESTS == true ]]; then
        print_status "Running customer portal tests..."
        $base_cmd tests/e2e/playwright/customer/
    fi

    # Run visual regression tests
    if [[ $VISUAL_TESTS == true ]] || [[ $ALL_TESTS == true ]]; then
        print_status "Running visual regression tests..."
        $base_cmd tests/e2e/playwright/visual/
    fi

    # Run accessibility tests
    if [[ $A11Y_TESTS == true ]] || [[ $ALL_TESTS == true ]]; then
        print_status "Running accessibility tests..."
        $base_cmd tests/e2e/playwright/accessibility/
    fi

    # Run end-to-end workflow tests
    if [[ $E2E_TESTS == true ]] || [[ $ALL_TESTS == true ]]; then
        print_status "Running end-to-end workflow tests..."
        $base_cmd tests/e2e/playwright/workflows/
    fi
}

# Function to generate reports
generate_reports() {
    if [[ $REPORT == true ]]; then
        print_status "Generating test reports..."

        # Open HTML report if available
        if [[ -d "tests/e2e/playwright-report" ]]; then
            print_success "HTML report generated at: tests/e2e/playwright-report/index.html"

            # Auto-open report on macOS/Linux if in interactive mode
            if [[ -t 0 ]] && [[ "$OSTYPE" == "darwin"* ]]; then
                open tests/e2e/playwright-report/index.html
            elif [[ -t 0 ]] && command -v xdg-open &> /dev/null; then
                xdg-open tests/e2e/playwright-report/index.html
            fi
        fi
    fi
}

# Function to cleanup
cleanup() {
    print_status "Running cleanup..."

    # Clean up temporary files
    if [[ -f ".env.testing.bak" ]]; then
        rm -f .env.testing.bak
    fi

    # Optional: Clean up test screenshots older than 7 days
    if [[ -d "tests/e2e/screenshots" ]]; then
        find tests/e2e/screenshots -name "*.png" -type f -mtime +7 -delete 2>/dev/null || true
    fi
}

# Main execution flow
main() {
    echo "🎭 Playwright Test Runner for Insurance Management System"
    echo "=========================================================="
    echo ""

    parse_args "$@"
    check_prerequisites
    setup_environment

    # Set trap for cleanup
    trap cleanup EXIT

    # Start Laravel development server if needed
    if ! curl -s http://localhost:8000 >/dev/null 2>&1; then
        print_status "Starting Laravel development server..."
        php artisan serve --port=8000 &
        SERVER_PID=$!

        # Wait for server to start
        sleep 5

        # Set trap to kill server on exit
        trap "kill $SERVER_PID 2>/dev/null || true; cleanup" EXIT
    fi

    # Run the tests
    run_tests

    print_success "All tests completed successfully!"

    # Generate reports
    generate_reports

    echo ""
    echo "🎉 Test execution completed!"
    echo ""

    # Show summary
    echo "📊 Test Results Summary:"
    echo "  - Check the HTML report for detailed results"
    echo "  - Screenshots saved in: tests/e2e/screenshots/"
    echo "  - Test artifacts in: tests/e2e/test-results/"
    echo ""
}

# Run main function with all arguments
main "$@"