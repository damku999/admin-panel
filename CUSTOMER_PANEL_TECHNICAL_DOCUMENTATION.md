# Customer Panel Technical Documentation

## Project Overview
This document provides comprehensive technical documentation for the Customer Panel system in the MIDAS insurance management application. The customer panel allows insurance customers to view their policies, manage their accounts, and access family insurance information.

---

## Table of Contents
1. [Authentication System](#authentication-system)
2. [Middleware Security Layers](#middleware-security-layers)
3. [Database Schema](#database-schema)
4. [Models and Relationships](#models-and-relationships)
5. [Controllers and Business Logic](#controllers-and-business-logic)
6. [Views and Templates](#views-and-templates)
7. [Route Configuration](#route-configuration)
8. [Security Features](#security-features)
9. [Family Access Control](#family-access-control)
10. [Audit Logging](#audit-logging)
11. [Session Management](#session-management)
12. [Password Management](#password-management)

---

## Authentication System

### Guard Configuration
- **Primary Guard**: `customer` (separate from admin authentication)
- **Guard Type**: Laravel's default session-based authentication
- **Password Hashing**: Laravel's default bcrypt hashing

### Authentication Flow
1. **Login Request** → `CustomerAuthController@login`
2. **Credential Validation** → Email + Password validation
3. **Status Check** → Only active customers can login (`status = true`)
4. **Rate Limiting** → Max 5 attempts per 15 minutes
5. **Success Actions**:
   - Session regeneration
   - Activity timestamp setting
   - Audit log creation
   - Conditional redirects based on password/email status

### Login Conditions
- **Required Fields**: Email (valid email format), Password (minimum 6 characters)
- **Status Requirement**: Customer must have `status = true`
- **Rate Limiting**: Enforced through Laravel's `ThrottlesLogins` trait
- **Session Security**: Session ID regenerated on successful login

---

## Middleware Security Layers

### 1. CustomerAuth Middleware
```php
Location: app/Http/Middleware/CustomerAuth.php
Purpose: Basic authentication and status verification
```

**Conditions Checked:**
- Customer is authenticated (`Auth::guard('customer')->check()`)
- Customer status is active (`customer.status = true`)
- **Action on Failure**: Logout and redirect to login

### 2. VerifyFamilyAccess Middleware
```php
Location: app/Http/Middleware/VerifyFamilyAccess.php
Purpose: Family-specific route access control
```

**Conditions Checked:**
- Customer is authenticated
- Customer status is active
- Customer has family group (`family_group_id IS NOT NULL`)
- Family group is active (`family_groups.status = true`)
- **Action on Failure**: Redirect to dashboard with appropriate message

### 3. SecureSession Middleware
```php
Location: app/Http/Middleware/SecureSession.php
Purpose: Session security enhancements (Currently disabled but available)
```

**Features (When Enabled):**
- Periodic session regeneration (every 30 minutes)
- Session timeout enforcement
- Session integrity validation
- Security headers application
- **Note**: Currently commented out in implementation

### 4. CustomerSessionTimeout Middleware
```php
Location: app/Http/Middleware/CustomerSessionTimeout.php
Purpose: Automatic session timeout management
```

**Configuration:**
- **Timeout Duration**: 60 minutes (configurable via `config('session.customer_timeout')`)
- **Activity Tracking**: Updates `customer_last_activity` session variable
- **Critical Operations Skip**: Password changes, logout, security operations
- **Timeout Actions**: Session invalidation, audit logging, forced logout

---

## Database Schema

### customers Table
```sql
Primary Key: id (bigint, auto_increment)
```

**Core Fields:**
- `name` (varchar 125, required)
- `email` (varchar 125, nullable, unique for authentication)
- `mobile_number` (varchar 125, nullable)
- `status` (tinyint, default 1 - controls access)
- `type` (enum: 'Corporate', 'Retail')

**Family Relations:**
- `family_group_id` (bigint, nullable, FK to family_groups)

**Authentication Fields:**
- `password` (varchar 255, hashed)
- `password_changed_at` (timestamp)
- `must_change_password` (tinyint, default 0)
- `email_verified_at` (timestamp)
- `email_verification_token` (varchar 255)
- `password_reset_token` (varchar 255)
- `password_reset_expires_at` (timestamp)
- `password_reset_sent_at` (timestamp)
- `remember_token` (varchar 100)

**Document Fields:**
- `pan_card_number`, `pan_card_path`
- `aadhar_card_number`, `aadhar_card_path`
- `gst_number`, `gst_path`

**Personal Info:**
- `date_of_birth` (date)
- `wedding_anniversary_date` (date)
- `engagement_anniversary_date` (date)

### family_groups Table
```sql
Primary Key: id (bigint, auto_increment)
```

**Fields:**
- `name` (varchar 255, required)
- `family_head_id` (bigint, FK to customers.id)
- `status` (tinyint, default 1)

### family_members Table
```sql
Primary Key: id (bigint, auto_increment)
```

**Fields:**
- `family_group_id` (bigint, FK to family_groups.id)
- `customer_id` (bigint, FK to customers.id)
- `relationship` (varchar 255)
- `is_head` (tinyint, default 0)
- `status` (tinyint, default 1)

### customer_audit_logs Table
```sql
Primary Key: id (bigint, auto_increment)
```

**Fields:**
- `customer_id` (bigint, FK to customers.id)
- `action` (string) - Type of action performed
- `resource_type` (string, nullable) - Resource affected
- `resource_id` (bigint, nullable) - Resource ID
- `description` (text) - Human readable description
- `metadata` (text, JSON) - Additional data
- `ip_address` (string) - Client IP
- `user_agent` (text) - Browser information
- `session_id` (string) - Session identifier
- `success` (boolean) - Action success status
- `failure_reason` (text, nullable) - Failure details

**Indexes:**
- `customer_action_created_idx` (customer_id, action, created_at)
- `action_created_idx` (action, created_at)
- `resource_idx` (resource_type, resource_id)

---

## Models and Relationships

### Customer Model
```php
Location: app/Models/Customer.php
Extends: Illuminate\Foundation\Auth\User
```

**Key Relationships:**
- `familyGroup()` - BelongsTo FamilyGroup
- `familyMember()` - HasOne FamilyMember
- `familyMembers()` - HasMany FamilyMember (all family members)
- `insurance()` - HasMany CustomerInsurance
- `auditLogs()` - HasMany CustomerAuditLog

**Important Methods:**

#### Family Methods
- `hasFamily()` - Returns bool if customer belongs to family
- `isFamilyHead()` - Returns bool if customer is family head
- `isInSameFamilyAs(Customer $customer)` - Checks same family membership
- `getViewableInsurance()` - Returns policies customer can view (own + family if head)

#### Security Methods
- `canViewSensitiveDataOf(Customer $customer)` - Permission check
- `getPrivacySafeData()` - Returns masked data for privacy
- `validateFamilyGroupId($id)` - SQL injection prevention

#### Authentication Methods
- `needsPasswordChange()` - Checks must_change_password flag
- `hasVerifiedEmail()` - Checks email_verified_at
- `generatePasswordResetToken()` - Creates secure 64-char token with 1-hour expiry
- `verifyPasswordResetToken($token)` - Validates token and expiry
- `changePassword($newPassword)` - Updates password and clears flags

### CustomerAuditLog Model
```php
Location: app/Models/CustomerAuditLog.php
```

**Static Methods:**
- `logAction($action, $description, $metadata)` - General action logging
- `logPolicyAction($action, $policy, $description, $metadata)` - Policy-specific logging
- `logFailure($action, $reason, $metadata)` - Failure logging

---

## Controllers and Business Logic

### CustomerAuthController
```php
Location: app/Http/Controllers/Auth/CustomerAuthController.php
```

**Key Features:**
- Extends Laravel's base Controller
- Uses `ThrottlesLogins` trait for rate limiting
- Custom guard implementation for customer authentication
- Comprehensive audit logging for all actions

#### Authentication Methods

**showLoginForm()**
- Returns login view
- No special logic

**login(Request $request)**
- Validates credentials (email/password)
- Checks rate limiting (5 attempts/15 minutes)
- Validates customer status
- Logs successful/failed attempts
- Handles post-login redirections

**logout(Request $request)**
- Logs logout action
- Invalidates session
- Regenerates CSRF token
- Redirects to login with success message

#### Dashboard Method

**dashboard()**
- Loads customer with family relationships
- Retrieves family policies (with SQL injection protection)
- Calculates policy expiration warnings (30 days)
- Handles family access errors gracefully
- Returns dashboard view with data

#### Password Management

**changePassword(Request $request)**
- Validates current password
- Enforces new password requirements (min 8 chars, confirmed)
- Updates password and clears change requirement
- Marks email as verified

**Password Reset Flow:**
1. `showPasswordResetForm()` - Display form
2. `sendPasswordResetLink()` - Generate token, send email (TODO: email sending)
3. `showPasswordResetFormWithToken($token)` - Validate token, show form
4. `resetPassword()` - Validate token, update password, clear token

#### Policy Access Methods

**showPolicies()**
- Checks family access
- Loads viewable policies based on role
- Categorizes by active/expired status
- Logs policy list access

**showPolicyDetail($policyId)**
- Loads specific policy with relationships
- Calculates expiry status
- Logs policy access

**downloadPolicy($policyId)**
- **Security Features:**
  - Path traversal attack prevention
  - File extension validation (PDF only)
  - Directory confinement checks
  - Authorization verification
- Comprehensive security logging

---

## Views and Templates

### Directory Structure
```
resources/views/customer/
├── auth/
│   ├── login.blade.php
│   ├── change-password.blade.php
│   ├── password-reset.blade.php
│   ├── reset-password.blade.php
│   └── verify-email.blade.php
├── partials/
│   ├── header.blade.php
│   └── logout-modal.blade.php
├── dashboard.blade.php
├── policies.blade.php
├── policy-detail.blade.php
└── profile.blade.php
```

### Dashboard Features
- **Customer Info Card**: Name, email, mobile, role (head/member)
- **Family Information**: Group name, family head, member list
- **Policy Expiration Alerts**: Policies expiring within 30 days
- **Family Policy List**: All viewable policies with status indicators
- **Quick Actions**: Password change, policy access, profile, help

### Login View Features
- Clean, professional interface
- Error message display
- Remember me functionality
- Password reset link
- Admin login redirect

---

## Route Configuration

### Route Groups and Middleware

**Public Routes** (Rate Limited: 10 requests/minute)
```php
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/login', 'showLoginForm');
    Route::post('/login', 'login');
});
```

**Password Reset Routes** (Rate Limited: 5 requests/minute)
```php
Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/password/reset', 'showPasswordResetForm');
    Route::post('/password/email', 'sendPasswordResetLink');
    Route::get('/password/reset/{token}', 'showPasswordResetFormWithToken');
    Route::post('/password/reset', 'resetPassword');
});
```

**Email Verification** (Rate Limited: 3 requests/minute)
```php
Route::middleware(['throttle:3,1'])->group(function () {
    Route::get('/email/verify/{token}', 'verifyEmail');
});
```

**Authenticated Routes** (Middleware: `auth:customer`, `customer.secure`, `customer.timeout`)
- Rate Limited: 60 requests/minute
- Session timeout enforcement
- Security headers

**Family-Specific Routes** (Additional Middleware: `customer.family`)
- Policy viewing and downloading
- Family data access

---

## Security Features

### 1. Authentication Security
- **Password Hashing**: Laravel's bcrypt with salt
- **Rate Limiting**: Failed attempt throttling
- **Session Management**: Automatic regeneration
- **Status Verification**: Active customer requirement

### 2. Authorization Security
- **Guard Separation**: Customer vs Admin authentication
- **Family Access Control**: Head vs Member permissions
- **Policy Access**: Same-family verification
- **Route Protection**: Middleware stacking

### 3. Input Security
- **SQL Injection Prevention**: 
  - Parameterized queries
  - Family Group ID validation
  - Numeric type enforcement
- **File Upload Security**:
  - Path traversal prevention
  - Extension validation
  - Directory confinement
  - Filename sanitization

### 4. Session Security
- **Timeout Management**: 60-minute inactivity timeout
- **Activity Tracking**: Last activity timestamps
- **Session Invalidation**: On logout and timeout
- **Token Regeneration**: CSRF protection

### 5. Data Privacy
- **Email Masking**: Partial display for privacy
- **Mobile Masking**: Partial display for privacy
- **Sensitive Data Access**: Permission-based viewing
- **Audit Logging**: All actions tracked

---

## Family Access Control

### Permission Model
```
Family Head:
├── View all family member policies
├── Download all family policy documents
├── Access all family member information
└── Dashboard shows all family data

Family Member:
├── View only own policies
├── Download only own policy documents
├── View basic family member information
└── Dashboard shows limited family data
```

### Implementation Details

**Family Head Detection:**
```php
public function isFamilyHead(): bool
{
    return $this->familyMember?->is_head === true;
}
```

**Policy Access Control:**
```php
public function getViewableInsurance()
{
    if ($this->isFamilyHead()) {
        // Head sees all family policies
        return CustomerInsurance::whereHas('customer', function ($query) {
            $query->where('family_group_id', $this->family_group_id);
        });
    } else {
        // Member sees only own policies
        return $this->insurance();
    }
}
```

### Security Validations
- Family group existence verification
- Family group active status check
- Customer membership verification
- SQL injection prevention in family queries

---

## Audit Logging

### Comprehensive Logging System
- **All Customer Actions**: Login, logout, policy access
- **Security Events**: Failed logins, timeout events, injection attempts
- **File Access**: Policy downloads with security validations
- **Administrative Actions**: Password changes, email verification

### Log Data Structure
```php
[
    'customer_id' => 'Acting customer ID',
    'action' => 'Action type identifier',
    'resource_type' => 'Affected resource type (policy, etc.)',
    'resource_id' => 'Affected resource ID',
    'description' => 'Human readable description',
    'metadata' => 'Additional structured data',
    'ip_address' => 'Client IP address',
    'user_agent' => 'Browser/client information',
    'session_id' => 'Session identifier',
    'success' => 'Boolean success indicator',
    'failure_reason' => 'Failure details if applicable'
]
```

### Security Event Examples
- **SQL Injection Attempts**: Invalid family group IDs
- **Path Traversal Attempts**: Invalid file paths
- **Authorization Failures**: Unauthorized resource access
- **Session Security**: Timeout events, force logouts

---

## Session Management

### Configuration
- **Timeout**: 60 minutes of inactivity
- **Activity Tracking**: Updates on each request
- **Critical Operations**: Skip timeout for security actions
- **AJAX Handling**: Special timeout handling for AJAX requests

### Timeout Behavior
```php
Critical Operations (No Timeout):
├── Password change operations
├── Logout process
├── Email verification
└── Password reset completion

Regular Operations (60min Timeout):
├── Dashboard access
├── Policy viewing
├── Profile access
└── General navigation
```

### Session Security
- **Session ID Regeneration**: On login, periodically during use
- **Session Invalidation**: Complete data clearing on logout/timeout
- **Token Regeneration**: CSRF tokens updated on security events
- **Activity Monitoring**: Last activity timestamps maintained

---

## Password Management

### Password Security Features

**Default Password Generation:**
- 8-character random string
- Uppercase letters and numbers
- Automatic "must change" flag
- Email verification requirement

**Password Change Requirements:**
- Current password verification
- Minimum 8 characters
- Confirmation required
- Automatic email verification on change

**Password Reset Security:**
- **Token Generation**: 64-character cryptographically secure token
- **Expiration**: 1-hour validity
- **Single Use**: Token cleared after successful reset
- **Secure Comparison**: Hash-based equality checking

### Password Reset Flow
1. **Request Reset**: Email validation, token generation
2. **Token Validation**: Expiry check, secure comparison
3. **Password Update**: Hash generation, flag clearing
4. **Security Logging**: Comprehensive audit trail
5. **Session Management**: Force re-authentication

---

## Error Handling and Edge Cases

### Graceful Error Handling
- **Invalid Family Data**: Graceful fallback with error messages
- **Missing Policies**: Appropriate "no data" displays
- **File Access Errors**: Security-conscious error messages
- **Session Timeouts**: Clear messaging and redirect handling

### Security Edge Cases
- **Inactive Family Groups**: Automatic access revocation
- **Customer Status Changes**: Real-time access control
- **Token Expiration**: Clean error handling
- **File Security Violations**: Comprehensive logging and blocking

---

## Configuration Dependencies

### Laravel Configuration
- **Authentication Guards**: Customer guard configuration
- **Session Configuration**: Timeout settings
- **Rate Limiting**: Request throttling configuration
- **File Storage**: Document storage configuration

### Environment Variables
```env
SESSION_CUSTOMER_TIMEOUT=60  # Customer session timeout in minutes
APP_DEBUG=false             # Security header control
```

### Database Requirements
- **Foreign Key Constraints**: Referential integrity
- **Index Optimization**: Performance for large datasets
- **Soft Deletes**: Data preservation with access control

---

## API Endpoints Summary

### Authentication Endpoints
```
GET  /customer/login                    - Show login form
POST /customer/login                    - Process login
POST /customer/logout                   - Process logout
```

### Password Management
```
GET  /customer/password/reset           - Show reset request form
POST /customer/password/email           - Send reset link
GET  /customer/password/reset/{token}   - Show reset form
POST /customer/password/reset           - Process password reset
GET  /customer/change-password          - Show change form
POST /customer/change-password          - Process password change
```

### Email Verification
```
GET  /customer/email/verify/{token}     - Verify email address
GET  /customer/email/verify-notice      - Show verification notice
POST /customer/email/resend             - Resend verification email
```

### Dashboard and Policies
```
GET  /customer/dashboard                - Main dashboard
GET  /customer/profile                  - Customer profile
GET  /customer/policies                 - Policy list (family access required)
GET  /customer/policies/{id}            - Policy details (family access required)
GET  /customer/policies/{id}/download   - Download policy document (family access required)
```

---

## Rate Limiting Configuration

### Endpoint-Specific Limits
- **Login Attempts**: 10 per minute
- **Password Reset**: 5 per minute  
- **Email Verification**: 3 per minute
- **Authenticated Routes**: 60 per minute
- **Policy Downloads**: 10 per minute

### Security Considerations
- **IP-based Limiting**: Per-client restrictions
- **Graduated Responses**: Different limits for different actions
- **Bypass for Critical Operations**: Emergency access scenarios

---

## Monitoring and Maintenance

### Regular Monitoring Points
- **Failed Login Attempts**: Security monitoring
- **Session Timeout Rates**: User experience metrics
- **Policy Access Patterns**: Usage analytics
- **Error Rates**: System health monitoring

### Maintenance Tasks
- **Audit Log Cleanup**: Regular log rotation
- **Session Data Cleanup**: Expired session removal
- **Password Reset Token Cleanup**: Expired token removal
- **Security Review**: Regular security assessment

---

## Future Enhancements

### Potential Improvements
- **Two-Factor Authentication**: Enhanced security
- **Email Integration**: Automated email sending
- **Mobile App API**: Mobile application support
- **Advanced Reporting**: Usage and security reporting
- **Real-time Notifications**: Policy expiry alerts

### Technical Debt
- **Email System**: Complete email integration implementation
- **Security Headers**: Full SecureSession middleware activation
- **Advanced Authorization**: Policy-based permissions
- **Performance Optimization**: Query optimization and caching

---

## Emergency Procedures

### Security Incident Response
1. **Account Compromise**: Immediate password reset and session invalidation
2. **SQL Injection Detection**: Automatic blocking and admin notification
3. **File Security Breach**: Path validation and access logging
4. **Session Hijacking**: Session regeneration and security logging

### System Recovery
- **Database Backup**: Regular backup procedures
- **Session Recovery**: Session data reconstruction
- **Audit Trail**: Complete action history maintenance
- **User Communication**: Incident notification procedures

---

*This documentation reflects the current implementation as of the analysis date. For the most current information, refer to the actual source code and recent changes in the version control system.*