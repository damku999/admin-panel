# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an insurance management system built with Laravel 10 and Vue.js 2. It's a comprehensive admin panel for managing insurance quotations, customers, policies, and related business entities. The application includes both an admin interface and a customer portal.

## Development Commands

### Backend (Laravel)
```bash
# Install PHP dependencies
composer install

# Run database migrations with seeders
php artisan migrate --seed

# Generate application key
php artisan key:generate

# Start development server
php artisan serve

# Run tests
php artisan test
# OR
./vendor/bin/phpunit

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate IDE helper files (already configured)
php artisan ide-helper:generate
php artisan ide-helper:models

# Run scheduler (for renewal reminders)
php artisan schedule:run
```

### Frontend (Asset Compilation)
```bash
# Install Node.js dependencies
npm install

# Development build with file watching
npm run dev
# OR
npm run watch

# Production build
npm run prod

# Hot module replacement (for development)
npm run hot
```

## Architecture Overview

### Database Structure
The application manages insurance business with these core entities:
- **Users**: Admin system users with role-based permissions
- **Customers**: Insurance clients with family grouping support
- **Insurance Companies**: Partner insurance providers
- **Quotations**: Insurance quotes with multiple company comparisons
- **Customer Insurances**: Active policies linked to customers
- **Family Groups**: Customer family management with shared access

Key relationships:
- Customers can belong to Family Groups with one family head
- Quotations contain multiple QuotationCompany records for comparison
- Customer Insurances track active policies with renewal dates
- Comprehensive audit logging via CustomerAuditLog and Spatie ActivityLog

### Authentication System
- **Admin Authentication**: Standard Laravel Auth with Spatie Laravel Permission for roles/permissions
- **Customer Portal**: Separate authentication system (`CustomerAuthController`) with:
  - Email verification workflow
  - Family member access (shared login for family groups)
  - Session timeout and security features
  - Password reset functionality

### Frontend Architecture
- **Framework**: Vue.js 2 with Laravel Mix for asset compilation
- **Styling**: Bootstrap 5 with custom SCSS
- **Layout Structure**:
  - `layouts/app.blade.php`: Main admin layout
  - `layouts/customer.blade.php`: Customer portal layout
  - Modular Blade components in `common/` directory

### Key Services & Features
- **QuotationService**: Handles complex insurance quote generation and comparison
- **PdfGenerationService**: PDF generation for quotes and policies using DomPDF
- **WhatsAppApiTrait**: Integration for sending documents via WhatsApp
- **FileUploadService**: Centralized file handling
- **Export System**: Excel export functionality using Maatwebsite/Excel

### Security Features
- Rate limiting via `SecurityRateLimiter` middleware
- Session security with `SecureSession` middleware
- Customer session timeout management
- Family access verification
- CSRF protection and proper validation

## Key Configuration

### Laravel Packages
- **spatie/laravel-permission**: Role and permission management
- **spatie/laravel-activitylog**: Comprehensive audit logging
- **barryvdh/laravel-dompdf**: PDF generation
- **maatwebsite/excel**: Excel import/export
- **opcodesio/log-viewer**: Log management interface at `/webmonks-log-viewer`

### Database
- MySQL 8+ with enum columns (note: some schema introspection may fail with enum types)
- Soft deletes enabled on most models
- Audit trail tracking (created_by, updated_by, deleted_by)
- Family group relationships with unique constraints

### Asset Management
- Laravel Mix with Vue.js 2 support
- SCSS compilation from `resources/sass/app.scss`
- JavaScript compilation from `resources/js/app.js`
- Vue components in `resources/js/components/`

## Development Guidelines

### Model Conventions
- Use soft deletes (`SoftDeletes` trait)
- Include audit tracking with `TableRecordObserver` trait
- Follow relationships: Customer -> FamilyGroup -> FamilyMember pattern
- Use proper fillable/guarded attributes

### Controller Patterns
- Resource controllers for CRUD operations
- Form Request validation (`StoreCustomerRequest`, `UpdateCustomerRequest`, etc.)
- Export functionality follows consistent pattern (see `BrokerController@export`)
- Status update methods follow pattern: `update/status/{id}/{status}`

### Route Organization
- Admin routes use resource patterns
- Customer portal routes prefixed with `customer/`
- API routes in `api/` group (currently minimal)
- Export routes follow pattern: `{resource}/export`

### File Structure
- Controllers organized by domain (Customer, Broker, Insurance, etc.)
- Models in `app/Models/` with proper relationships
- Exports in `app/Exports/` following Maatwebsite/Excel patterns
- Mail classes in `app/Mail/` for customer communications
- Services in `app/Services/` for business logic

### Testing
- PHPUnit configured in `phpunit.xml`
- Test database configuration separate from main database
- Feature and Unit test structure in `tests/` directory

## Customer Portal Features
- Separate authentication system from admin
- Family group sharing (multiple customers can share one login)
- Policy viewing and document downloads
- Quotation history and downloads
- Email verification and password reset flows
- Audit logging for all customer actions

## Common Tasks

### Adding New Insurance Entity
1. Create migration with audit fields and soft deletes
2. Generate model with relationships and traits
3. Create controller extending base patterns
4. Add export functionality using Maatwebsite/Excel
5. Create form requests for validation
6. Add routes following resource pattern
7. Create Blade views following existing structure

### Working with Family Groups
- Always check family relationships when dealing with customer data
- Use `VerifyFamilyAccess` middleware for family-specific operations
- Family head has administrative privileges over family members

### PDF Generation
- Use `PdfGenerationService` for consistent PDF handling
- Templates in `resources/views/pdfs/`
- Support for custom paper sizes and orientations

## Environment Setup
- Default admin login: admin@admin.com / Admin@123#
- Requires MySQL database configuration in `.env`
- WhatsApp API configuration for document sharing features
- File storage configuration for document uploads