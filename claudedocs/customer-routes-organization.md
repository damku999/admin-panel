# Customer Portal Routes Organization

## Overview

The customer portal routes have been moved to a separate file for better code organization and maintainability. This separation provides clear boundaries between admin and customer functionality.

## File Structure

```
routes/
├── web.php          # Admin routes and core application routes
├── api.php          # API routes
└── customer.php     # Customer portal routes (NEW)
```

## Route Registration

The customer routes are automatically registered in `RouteServiceProvider.php`:

```php
// Customer Portal Routes (separate file for better organization)
Route::middleware('web')
    ->namespace($this->namespace)
    ->group(base_path('routes/customer.php'));
```

## Customer Routes Structure

### 🔓 Public Routes (Unauthenticated)

| Route | Method | Controller Method | Purpose |
|-------|--------|------------------|---------|
| `customer/login` | GET/POST | `showLoginForm`, `login` | Customer authentication |
| `customer/password/reset` | GET/POST | Password reset flow | Password recovery |
| `customer/email/verify/{token}` | GET | `verifyEmail` | Email verification |

### 🔒 Authenticated Routes (Customer Login Required)

| Route | Method | Controller Method | Purpose |
|-------|--------|------------------|---------|
| `customer/dashboard` | GET | `dashboard` | Main customer dashboard |
| `customer/profile` | GET | `showProfile` | Customer profile viewing |
| `customer/change-password` | GET/POST | Password change | Security management |
| `customer/logout` | POST | `logout` | Session termination |

### 👨‍👩‍👧‍👦 Family Group Routes (Family Membership Required)

| Route | Method | Controller Method | Purpose |
|-------|--------|------------------|---------|
| `customer/policies` | GET | `showPolicies` | Insurance policies listing |
| `customer/policies/{policy}` | GET | `showPolicyDetail` | Policy details |
| `customer/policies/{policy}/download` | GET | `downloadPolicy` | Policy documents |
| `customer/quotations` | GET | `showQuotations` | Quotations listing |
| `customer/quotations/{quotation}` | GET | `showQuotationDetail` | Quote details |
| `customer/quotations/{quotation}/download` | GET | `downloadQuotation` | Quote documents |

### 👑 Family Head Routes (Family Management)

| Route | Method | Controller Method | Purpose |
|-------|--------|------------------|---------|
| `customer/family-member/{member}/profile` | GET | `showFamilyMemberProfile` | Member profiles |
| `customer/family-member/{member}/change-password` | GET/POST | Family member password management | Security |

## Security Features

### Rate Limiting

| Route Type | Limit | Purpose |
|------------|-------|---------|
| Login attempts | 10/minute | Prevent brute force attacks |
| Password reset | 5/minute | Limit reset abuse |
| Email verification | 3/minute | Prevent spam |
| General routes | 60/minute | Standard protection |
| Downloads | 10/minute | Prevent resource abuse |

### Middleware Stack

1. **customer.auth**: Validates customer authentication
2. **customer.timeout**: Enforces session timeout
3. **customer.family**: Ensures family group membership
4. **throttle**: Rate limiting protection

## Benefits of Separation

### ✅ Improved Organization
- Clear separation between admin and customer functionality
- Easier to locate and maintain customer-specific routes
- Better code readability and documentation

### ✅ Enhanced Security
- Dedicated middleware stack for customer routes
- Granular rate limiting per route type
- Isolated authentication logic

### ✅ Better Maintainability
- Changes to customer routes don't affect admin routes
- Easier onboarding for new developers
- Simplified testing and debugging

### ✅ Scalability
- Customer portal can be extracted to microservice later
- Independent deployment possibilities
- Better performance monitoring

## Migration Status

✅ **Completed:**
- All customer routes moved to `routes/customer.php`
- RouteServiceProvider updated to register customer routes
- Original routes removed from `web.php`
- Documentation created
- Route functionality verified

✅ **Tested:**
- Customer login page: `http://localhost:8000/customer/login` ✅
- Route list verification: All 23 customer routes registered ✅
- Middleware and rate limiting preserved ✅

## Usage Examples

### Customer Authentication
```php
// Login
Route::get('/customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');

// Protected dashboard
Route::middleware(['customer.auth', 'customer.timeout'])
    ->get('/customer/dashboard', [CustomerAuthController::class, 'dashboard'])
    ->name('customer.dashboard');
```

### Family-Specific Access
```php
// Requires family membership
Route::middleware(['customer.auth', 'customer.family'])
    ->get('/customer/policies', [CustomerAuthController::class, 'showPolicies'])
    ->name('customer.policies');
```

This organization provides a solid foundation for the customer portal with clear separation of concerns and enhanced maintainability.