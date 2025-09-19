# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 10.49.0 insurance management system with dual-portal architecture running on PHP ^8.1:
- **Admin Portal**: Full-featured insurance management dashboard
- **Customer Portal**: Customer-facing interface for policy management

The system manages insurance policies, quotations, claims, customers, and related entities with role-based permissions using Spatie Laravel Permission.

## Architecture

### Clean Architecture Implementation
The codebase follows a clean architecture pattern with clear separation of concerns:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Business logic layer implementing service interfaces
- **Repositories**: Data access layer with repository pattern
- **Contracts**: Interfaces defining service and repository contracts
- **Models**: Eloquent models with relationships and business logic
- **Events/Listeners**: Event-driven architecture for notifications

### Key Design Patterns
- **Repository Pattern**: All data access through repository interfaces
- **Service Layer**: Business logic encapsulated in service classes
- **Dependency Injection**: Constructor injection throughout the application
- **Event-Driven**: Customer registration, policy updates trigger events
- **Trait-Based**: Reusable functionality through traits (WhatsAppApiTrait, HelperTrait, TableRecordObserver)

### Database Architecture
Multi-entity relationship system centered around:
- **Customers**: Core customer data with family grouping
- **Quotations**: Quote generation with multiple insurance company comparisons
- **Policies (customer_insurances)**: Active insurance policies
- **Claims**: Claims management with document tracking and stages
- **Insurance Companies**: Provider management with premium calculations

## Common Development Commands

### Laravel Commands
```bash
# Clear all caches and optimize
php artisan optimize:clear

# Run migrations with seeding
php artisan migrate --seed

# Start development server
php artisan serve

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Clear specific caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Asset Building
```bash
# Development build with watch
npm run dev
npm run watch

# Production build
npm run prod

# Separate portal builds
npm run build-admin
npm run build-customer
```

### Composer Scripts
```bash
# Combined development setup
composer run dev

# Development environment setup
composer run dev-setup
```

### Testing
```bash
# Run PHPUnit tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/Feature/CustomerTest.php

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

## Directory Structure

### Core Application Structure
- `app/Contracts/` - Service and repository interfaces
- `app/Services/` - Business logic implementation
- `app/Repositories/` - Data access layer
- `app/Http/Controllers/` - Request handling
- `app/Models/` - Eloquent models
- `app/Events/` - Event classes
- `app/Listeners/` - Event listeners
- `app/Traits/` - Reusable functionality
- `app/Exports/` - Excel export classes

### Frontend Assets
- `resources/js/admin/` - Admin portal JavaScript
- `resources/js/customer/` - Customer portal JavaScript
- `resources/sass/admin/` - Admin portal styles
- `resources/sass/customer/` - Customer portal styles
- `resources/views/` - Blade templates organized by feature

### Technology Stack
- **Backend**: Laravel 10.49.0, PHP ^8.1
- **Frontend**: Bootstrap 5.3.2, jQuery 3.7.1, Laravel Mix 6.0.49
- **Build Tools**: Sass 1.69.5, Playwright 1.55.0 for testing
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel UI with dual guard system

### Configuration
- Dual authentication system (admin/customer guards)
- Role-based permissions with Spatie Permission
- Activity logging with Spatie Activity Log
- File upload management
- WhatsApp API integration
- Excel export capabilities

## Key Features

### Insurance Management
- **Quotation System**: Multi-company quote comparison with ranking
- **Policy Management**: Full lifecycle from quotation to renewal
- **Claims Processing**: Document tracking with stages and liability details
- **Premium Calculation**: Complex premium calculations with add-on covers

### Customer Management
- **Family Grouping**: Family-based customer organization
- **Dual Authentication**: Separate admin and customer portals
- **Profile Management**: Customer self-service capabilities
- **Document Management**: Secure file uploads and downloads

### Reporting & Analytics
- **Dynamic Reports**: User-configurable column selection
- **Excel Exports**: Comprehensive export functionality
- **Activity Logging**: Full audit trail
- **Health Monitoring**: Application health checks

## Development Guidelines

### Code Organization
- Follow the existing repository/service pattern for new features
- Implement proper dependency injection
- Use form request classes for validation
- Maintain consistent naming conventions

### Database
- Use migrations for all schema changes
- Implement proper foreign key relationships
- Follow soft delete patterns where applicable
- Use database seeders for test data

### Frontend
- Maintain separation between admin and customer portals
- Use Bootstrap 5 consistently
- Follow the existing asset compilation structure
- Implement responsive design patterns

### Security
- All routes protected by appropriate middleware
- Role-based access control implemented
- CSRF protection enabled
- File upload security measures in place

## Testing Strategy
- Feature tests for business logic
- Unit tests for service classes
- Integration tests for repository layer
- Browser tests for critical user flows (Playwright available)

## Production Considerations
- Assets are versioned in production builds
- Console logging removed in production
- Proper error handling with user-friendly messages
- Health check endpoints for monitoring