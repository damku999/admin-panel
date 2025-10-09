# PHPDoc Documentation Standards Guide

## Quick Reference for Service Layer Documentation

This guide provides templates and examples for documenting service layer methods with comprehensive PHPDoc comments.

---

## Standard Template

```php
/**
 * One-line summary of what the method does (max 80 chars).
 *
 * Detailed explanation providing business context, implementation notes,
 * transaction boundaries, events fired, notifications sent, and any
 * important edge cases or behavioral notes.
 *
 * @param  TypeHint  $parameterName  Clear description of what this parameter represents
 * @param  AnotherType  $anotherParam  Description with examples if helpful (e.g., 'customer_welcome', 'renewal_30_days')
 * @return ReturnType  Description of what is returned and what it contains
 *
 * @throws ExceptionClass  Condition under which this exception is thrown
 * @throws AnotherException  Another exception condition
 */
public function methodName(TypeHint $parameterName, AnotherType $anotherParam): ReturnType
{
    // Implementation
}
```

---

## Documentation Elements

### 1. Summary Line
- **Purpose**: One-line description of method's purpose
- **Length**: Maximum 80 characters
- **Style**: Clear, concise, active voice
- **Examples**:
  ```php
  // Good
  Get paginated list of customers with filtering and search.
  Create new customer with document handling and welcome email.
  Send renewal reminder WhatsApp message to policy holder.

  // Bad (too vague)
  Get customers.
  Process customer.
  Send message.
  ```

### 2. Detailed Description
- **Purpose**: Provide business context and implementation details
- **Include**:
  - Business logic explanation
  - Transaction boundaries
  - Events dispatched
  - Notifications sent
  - Side effects
  - Edge cases
  - Important behavioral notes

- **Example**:
  ```php
  /**
   * Create a new customer with document handling and welcome email.
   *
   * This method orchestrates customer creation within a database transaction,
   * ensuring atomicity of the customer record, associated documents, and
   * the welcome email notification. If email sending fails, the entire
   * transaction is rolled back to maintain data consistency.
   *
   * Events fired: CustomerRegistered (async for audit logs)
   * Notifications: Welcome email (sync, blocks on failure)
   *
   * ...
   */
  ```

### 3. Parameter Documentation
- **Format**: `@param  Type  $name  Description`
- **Include**:
  - Proper type hint (including union types like `int|null`)
  - Parameter name exactly as in signature
  - Clear description of what the parameter represents
  - Examples for enum-like parameters

- **Examples**:
  ```php
  @param  StoreCustomerRequest  $request  Validated customer registration data including personal info and documents
  @param  int  $status  Status value (0 = inactive, 1 = active)
  @param  string  $notificationTypeCode  Notification type code (e.g., 'customer_welcome', 'renewal_30_days')
  @param  array  $data  Policy data including customer ID, company ID, dates, and premium information
  @param  int|null  $daysAhead  Number of days ahead to check for expiring policies (default 30)
  ```

### 4. Return Documentation
- **Format**: `@return  Type  Description`
- **Include**:
  - Proper return type (including `|null` if nullable)
  - Description of what is returned
  - Indicate if collection is empty vs null

- **Examples**:
  ```php
  @return Customer  The newly created customer instance with relationships loaded
  @return bool  True if update successful, false otherwise
  @return Collection  Collection of active customer records
  @return Customer|null  Customer instance if found, null otherwise
  @return array  Results array with 'total', 'sent', 'failed' counts and error details
  @return LengthAwarePaginator  Paginated policy collection with 10 items per page
  ```

### 5. Exception Documentation
- **Format**: `@throws  ExceptionClass  Condition`
- **Include**: All exceptions that can be thrown
- **Explain**: When and why the exception occurs

- **Examples**:
  ```php
  @throws \Exception  If email already exists in the system
  @throws \Exception  If email sending fails, triggering transaction rollback with user-friendly message
  @throws \InvalidArgumentException  If customer ID doesn't exist or status value is invalid
  @throws \Illuminate\Database\QueryException  On database constraint violations
  ```

---

## Common Patterns

### Pattern 1: Simple CRUD Methods

```php
/**
 * Get all active policies for dropdown selection.
 *
 * @return Collection  Collection of currently active policies
 */
public function getActivePolicies(): Collection
{
    return $this->policyRepository->getActive();
}
```

### Pattern 2: Transaction-Based Create/Update

```php
/**
 * Create a new insurance policy record.
 *
 * Creates a new policy within a database transaction to ensure data consistency.
 * All related data (customer, company, dates, premiums) are validated before
 * persisting to the database.
 *
 * @param  array  $data  Policy data including customer, company, dates, and premium information
 * @return CustomerInsurance  The newly created policy instance
 *
 * @throws \Illuminate\Database\QueryException  On foreign key constraint violations or database errors
 */
public function createPolicy(array $data): CustomerInsurance
{
    return $this->createInTransaction(
        fn () => $this->policyRepository->create($data)
    );
}
```

### Pattern 3: Complex Business Logic with Side Effects

```php
/**
 * Send renewal reminder WhatsApp message to policy holder.
 *
 * Sends contextual renewal reminder based on days remaining until policy expiration.
 * Uses notification template system with dynamic message selection:
 * - 30 days: Standard renewal notice
 * - 15 days: Urgent renewal reminder
 * - 7 days: Final reminder before expiration
 * - Expired: Immediate renewal required notice
 *
 * Logs send status for tracking renewal campaign effectiveness and customer
 * engagement metrics. Does not throw exceptions on WhatsApp API failures to
 * prevent blocking batch operations.
 *
 * @param  CustomerInsurance  $policy  The policy requiring renewal reminder
 * @return bool  True if message sent successfully, false otherwise
 */
public function sendRenewalReminder(CustomerInsurance $policy): bool
{
    // Implementation
}
```

### Pattern 4: Authorization and Access Control

```php
/**
 * Check if customer has permission to view specific policy.
 *
 * Enforces access control rules based on ownership and family relationships:
 * - Customers can always view their own policies
 * - Family heads can view all family member policies
 * - Non-family-head members cannot view others' policies
 *
 * Used to gate policy detail views and ensure data privacy compliance.
 *
 * @param  Customer  $customer  The customer requesting policy access
 * @param  CustomerInsurance  $policy  The policy to check access for
 * @return bool  True if customer can view policy, false otherwise
 */
public function canCustomerViewPolicy(Customer $customer, CustomerInsurance $policy): bool
{
    // Implementation
}
```

### Pattern 5: Batch/Bulk Operations

```php
/**
 * Send renewal reminders to multiple policies in bulk.
 *
 * Processes batch renewal reminders for policies expiring within specified days.
 * Each policy is processed individually to prevent single failures from blocking
 * the entire batch. Tracks success/failure rates for campaign effectiveness
 * monitoring and generates detailed error reports for failed deliveries.
 *
 * Results include:
 * - total: Number of policies processed
 * - sent: Number of successful deliveries
 * - failed: Number of failures
 * - errors: Array of failure details with policy info
 *
 * @param  int|null  $daysAhead  Number of days ahead to check (default 30)
 * @return array  Results array with 'total', 'sent', 'failed' counts and error details
 */
public function sendBulkRenewalReminders(?int $daysAhead = null): array
{
    // Implementation
}
```

### Pattern 6: Search and Filtering

```php
/**
 * Get paginated list of policies with comprehensive filtering.
 *
 * Retrieves policies with multiple filter options applied:
 * - search: Full-text search across policy number and customer name
 * - customer_id: Filter by specific customer
 * - insurance_company_id: Filter by insurance provider
 * - policy_type_id: Filter by policy type (vehicle, health, etc.)
 * - status: Filter by active/inactive status
 * - from_date/to_date: Filter by date range
 *
 * Results are paginated with 10 items per page and include all necessary
 * relationships for display (customer, company, type, etc.).
 *
 * @param  Request  $request  HTTP request with filter parameters
 * @return LengthAwarePaginator  Paginated policy collection with 10 items per page
 */
public function getPolicies(Request $request): LengthAwarePaginator
{
    // Implementation
}
```

---

## Business Context Keywords

Include these keywords in detailed descriptions when applicable:

### Transaction Keywords:
- "within a database transaction"
- "ensures atomicity"
- "rolled back on failure"
- "maintains data consistency"

### Event Keywords:
- "dispatches [EventName] event"
- "fires event for audit logging"
- "triggers async notification"
- "publishes event to queue"

### Notification Keywords:
- "sends WhatsApp message"
- "delivers email notification"
- "pushes notification to devices"
- "uses notification template system"

### Validation Keywords:
- "validates input data"
- "enforces business rules"
- "checks authorization"
- "verifies data integrity"

### Side Effect Keywords:
- "updates related records"
- "creates associated documents"
- "logs activity"
- "caches result"

---

## Type Hints Reference

### Laravel/Eloquent Types:
```php
Collection                  // Illuminate\Database\Eloquent\Collection
LengthAwarePaginator       // Illuminate\Contracts\Pagination\LengthAwarePaginator
Request                    // Illuminate\Http\Request
Model                      // Eloquent model instance
```

### Common Union Types:
```php
Model|null                 // Nullable model
int|null                   // Nullable integer
string|array              // String or array
Customer|Collection       // Single or multiple results
```

### Complex Return Types:
```php
array                      // Associative array (document structure in description)
bool                       // Success/failure status
void                       // No return value
mixed                      // Multiple possible types (avoid if possible)
```

---

## Common Mistakes to Avoid

### ❌ Too Vague
```php
/**
 * Process customer.
 *
 * @param  Customer  $customer
 * @return bool
 */
```

### ✅ Clear and Specific
```php
/**
 * Send onboarding email to newly registered customer.
 *
 * Sends welcome email using the EmailService and notification template system.
 * Logs errors but returns false on failure to prevent blocking customer creation.
 *
 * @param  Customer  $customer  The customer to send onboarding email to
 * @return bool  True if email sent successfully, false otherwise
 */
```

---

### ❌ Missing Business Context
```php
/**
 * Create customer.
 *
 * @param  StoreCustomerRequest  $request
 * @return Customer
 */
```

### ✅ Includes Business Context
```php
/**
 * Create a new customer with document handling and welcome email.
 *
 * This method orchestrates customer creation within a database transaction,
 * ensuring atomicity of the customer record, associated documents, and
 * the welcome email notification. If email sending fails, the entire
 * transaction is rolled back to maintain data consistency.
 *
 * Events fired: CustomerRegistered (async for audit logs)
 * Notifications: Welcome email (sync, blocks on failure)
 *
 * @param  StoreCustomerRequest  $request  Validated customer registration data including personal info and documents
 * @return Customer  The newly created customer instance with relationships loaded
 *
 * @throws \Exception  If email already exists in the system
 * @throws \Exception  If email sending fails, triggering transaction rollback
 */
```

---

## Formatting Commands

### Check Formatting:
```bash
php vendor/bin/pint app/Services/YourService.php --test
```

### Apply Formatting:
```bash
php vendor/bin/pint app/Services/YourService.php
```

### Format All Services:
```bash
php vendor/bin/pint app/Services/
```

---

## IDE Integration

### PHPStorm:
- Documentation appears in Quick Documentation popup (Ctrl+Q / Cmd+J)
- Parameter hints show during method calls
- Autocomplete shows return types

### VS Code with PHP Intelephense:
- Hover over method name shows documentation
- Parameter hints during typing
- Go to Definition includes documentation

---

## Checklist for Each Method

Before marking a method as documented, verify:

- [ ] Summary line is clear and under 80 characters
- [ ] Detailed description includes business context
- [ ] Transaction boundaries are documented (if applicable)
- [ ] Events/notifications are mentioned (if applicable)
- [ ] All parameters have type and description
- [ ] Return type is documented with description
- [ ] Exceptions are documented (if thrown)
- [ ] Edge cases are noted (if any)
- [ ] Laravel Pint formatting applied
- [ ] Type hints match method signature exactly

---

## References

- [Laravel API Documentation](https://laravel.com/api/10.x/index.html)
- [PHPDoc Standard](https://docs.phpdoc.org/3.0/)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

---

**Last Updated**: 2025-10-09
**Maintained By**: Development Team
**Version**: 1.0
