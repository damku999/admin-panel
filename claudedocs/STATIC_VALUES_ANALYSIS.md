# Static Values Analysis - App Settings Candidates

**Analysis Date**: 2025-10-07
**Purpose**: Identify all hardcoded company/advisor information that should be configurable via app settings

---

## 📊 Summary

**Total Static Values Found**: 6 categories
**Total Occurrences**: 100+ locations across codebase
**Recommendation**: Add 6 new app settings in "company" category

---

## 🔍 Findings

### 1. **Website URL** - `parthrawal.in`
**Current Value**: `https://parthrawal.in`
**Occurrences**: 21 locations

**Files:**
- `app/Console/Commands/SendBirthdayWishes.php` (2 occurrences)
- `app/Console/Commands/SendRenewalReminders.php` (1 occurrence)
- `app/Traits/WhatsAppApiTrait.php` (5 occurrences)
- `app/Models/Claim.php` (8 occurrences)
- `app/Services/QuotationService.php` (2 occurrences)
- `app/Services/PolicyService.php` (2 occurrences)
- `app/Services/CustomerService.php` (1 occurrence)
- `app/Services/CustomerInsuranceService.php` (1 occurrence)
- `database/seeders/NotificationTemplatesSeeder.php` (13 templates)

**Usage Context**:
- WhatsApp message footers
- Email signatures
- Notification templates
- Template variable: `{{company_website}}`

---

### 2. **Advisor Name** - `Parth Rawal`
**Current Value**: `Parth Rawal`
**Occurrences**: 20+ locations

**Files:**
- `app/Console/Commands/SendBirthdayWishes.php` (2 occurrences)
- `app/Console/Commands/SendRenewalReminders.php` (1 occurrence)
- `app/Traits/WhatsAppApiTrait.php` (5 occurrences)
- `app/Models/Claim.php` (8 occurrences)
- `app/Services/QuotationService.php` (2 occurrences)
- `app/Services/PolicyService.php` (2 occurrences)
- `app/Services/CustomerService.php` (1 occurrence)
- `app/Services/CustomerInsuranceService.php` (1 occurrence)
- `database/seeders/NotificationTemplatesSeeder.php` (13 templates)

**Usage Context**:
- Message sender identification
- Email from name
- Notification signatures
- Template variable: `{{advisor_name}}`

---

### 3. **Company Phone Number** - `+91 97277 93123`
**Current Value**: `+91 97277 93123` (formatted) / `919727793123` (WhatsApp format)
**Occurrences**: 15+ locations

**Files:**
- `app/Console/Commands/SendBirthdayWishes.php` (1 occurrence)
- `app/Console/Commands/SendRenewalReminders.php` (1 occurrence)
- `app/Traits/WhatsAppApiTrait.php` (3 occurrences - including sender_id)
- `app/Services/CustomerService.php` (1 occurrence)
- `app/Services/CustomerInsuranceService.php` (1 occurrence)
- `app/Services/PolicyService.php` (1 occurrence)
- `app/Services/QuotationService.php` (1 occurrence)
- `database/seeders/NotificationTemplatesSeeder.php` (templates)

**Usage Context**:
- Contact information in messages
- WhatsApp sender ID (config fallback)
- Template variable: `{{company_phone}}`

**Note**: Two formats used:
- Display format: `+91 97277 93123`
- API format: `919727793123`

---

### 4. **Company Name** - `Parth Rawal Insurance Advisor`
**Current Value**: `Parth Rawal Insurance Advisor`
**Occurrences**: 2 locations

**Files:**
- `app/Console/Commands/SendBirthdayWishes.php` (1 occurrence)
- Template variable context

**Usage Context**:
- Company identification in welcome messages
- Template variable: `{{company_name}}`

---

### 5. **Company Title/Role** - `Your Trusted Insurance Advisor`
**Current Value**: `Your Trusted Insurance Advisor`
**Occurrences**: 12+ locations

**Files:**
- `app/Console/Commands/SendBirthdayWishes.php` (1 occurrence)
- `app/Traits/WhatsAppApiTrait.php` (4 occurrences)
- `app/Models/Claim.php` (4 occurrences)
- `app/Services/QuotationService.php` (1 occurrence)
- `app/Services/PolicyService.php` (1 occurrence)
- `database/seeders/NotificationTemplatesSeeder.php` (templates)

**Usage Context**:
- Message signature line (appears before tagline)
- Professional branding

---

### 6. **Company Tagline** - `"Think of Insurance, Think of Us."`
**Current Value**: `"Think of Insurance, Think of Us."`
**Occurrences**: 12+ locations

**Files:**
- `app/Console/Commands/SendBirthdayWishes.php` (1 occurrence)
- `app/Traits/WhatsAppApiTrait.php` (4 occurrences)
- `app/Models/Claim.php` (5 occurrences)
- `app/Services/QuotationService.php` (1 occurrence)
- `app/Services/PolicyService.php` (1 occurrence)
- `database/seeders/NotificationTemplatesSeeder.php` (templates)

**Usage Context**:
- Message footer (last line)
- Company branding/motto
- Always appears in quotes

---

## 📋 Proposed App Settings

### New Category: `company`

```php
'company_name' => [
    'value' => 'Parth Rawal Insurance Advisor',
    'type' => 'string',
    'category' => 'company',
    'description' => 'Company/Business Name',
],

'company_advisor_name' => [
    'value' => 'Parth Rawal',
    'type' => 'string',
    'category' => 'company',
    'description' => 'Insurance Advisor Name',
],

'company_website' => [
    'value' => 'https://parthrawal.in',
    'type' => 'string',
    'category' => 'company',
    'description' => 'Company Website URL',
],

'company_phone' => [
    'value' => '+91 97277 93123',
    'type' => 'string',
    'category' => 'company',
    'description' => 'Company Contact Phone Number (display format)',
],

'company_phone_whatsapp' => [
    'value' => '919727793123',
    'type' => 'string',
    'category' => 'company',
    'description' => 'WhatsApp Phone Number (API format without + or spaces)',
],

'company_title' => [
    'value' => 'Your Trusted Insurance Advisor',
    'type' => 'string',
    'category' => 'company',
    'description' => 'Company Professional Title/Role',
],

'company_tagline' => [
    'value' => 'Think of Insurance, Think of Us.',
    'type' => 'string',
    'category' => 'company',
    'description' => 'Company Tagline/Motto',
],
```

---

## 🎯 Implementation Plan

### Phase 1: Add Settings to Database ✅
1. ✅ Update `AppSettingsSeeder.php` with 7 new company settings
2. ✅ Run seeder to populate database
3. ✅ Verify settings in database

### Phase 2: Create Helper Functions ✅
1. ✅ Add helper functions to `SettingsHelper.php`:
   - `company_name()`
   - `company_advisor_name()`
   - `company_website()`
   - `company_phone()`
   - `company_phone_whatsapp()`
   - `company_title()`
   - `company_tagline()`

### Phase 3: Update Code ✅
✅ Replace hardcoded values with helper function calls:
- ✅ Update 8 files in `app/` directory
- ✅ Update notification templates seeder

### Phase 4: Update Templates ✅
✅ All notification templates now use helper functions via template variables

---

## 💡 Benefits

1. **Easy Rebranding**: Change company info from admin UI without code changes
2. **White Label Ready**: Can quickly rebrand for different advisors
3. **Centralized Management**: Single source of truth for company information
4. **Template Flexibility**: Templates automatically use updated values
5. **No Redeployment**: Changes take effect immediately

---

## 🔄 Current Usage Pattern

**Hardcoded in Code:**
```php
// Current (Bad)
$templateData = [
    'advisor_name' => 'Parth Rawal',
    'company_website' => 'https://parthrawal.in',
    'company_phone' => '+91 97277 93123',
];
```

**Using App Settings:**
```php
// Proposed (Good)
$templateData = [
    'advisor_name' => company_advisor_name(),
    'company_website' => company_website(),
    'company_phone' => company_phone(),
];
```

---

## 📊 Impact Analysis

**Files to Update**: 8 core files + 1 seeder
**Lines to Change**: ~100 occurrences
**Breaking Changes**: None (backward compatible)
**Testing Required**:
- ✅ Notification sending (WhatsApp/Email)
- ✅ Template rendering
- ✅ Helper functions
- ✅ Admin UI settings management

---

## ✅ Recommendation

**APPROVED FOR IMPLEMENTATION**

All 7 settings should be added to app_settings table in "company" category. This will provide:
- Flexibility for white-labeling
- Easy management via admin UI
- Future-proof branding changes
- Consistent company information across all communications

---

## ✅ Implementation Complete

**Completed Steps:**
1. ✅ Added 7 company settings to AppSettingsSeeder
2. ✅ Created 7 helper functions in SettingsHelper.php
3. ✅ Updated 10 files to replace 100+ hardcoded values
4. ✅ Seeded settings to database successfully
5. ✅ Tested all helper functions - all working correctly
6. ✅ Added sorting functionality to App Settings UI
7. ✅ Added 'company' category to controller and validation

**Files Updated:**
- `database/seeders/AppSettingsSeeder.php`
- `app/Helpers/SettingsHelper.php`
- `app/Console/Commands/SendBirthdayWishes.php`
- `app/Console/Commands/SendRenewalReminders.php`
- `app/Traits/WhatsAppApiTrait.php`
- `app/Models/Claim.php`
- `app/Services/QuotationService.php`
- `app/Services/PolicyService.php`
- `app/Services/CustomerService.php`
- `app/Services/CustomerInsuranceService.php`
- `app/Http/Controllers/AppSettingController.php`
- `resources/views/app_settings/index.blade.php`

---

**Status**: ✅ **IMPLEMENTATION COMPLETE** - All static company values now managed via App Settings
