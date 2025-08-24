# Development Guidelines - Midas Insurance System

## 🚨 **CRITICAL REMINDERS - ALWAYS CHECK FIRST** 

### **1. Delete Confirmation Pattern**
**❌ WRONG:** Creating new delete confirmation views or inline confirmations
```blade
<!-- DON'T DO THIS -->
<form onsubmit="return confirm('Are you sure?')">
    @method('DELETE')
    <button>Delete</button>
</form>
```

**✅ CORRECT:** Use existing common delete modal structure
```blade
<!-- USE THIS PATTERN -->
<a class="btn btn-danger btn-sm" href="javascript:void(0);" 
   onclick="delete_conf_common('{{ $item->id }}','ModelName', 'Display Name: {{ $item->name }}', '{{ route('items.index') }}');">
    <i class="fas fa-trash"></i>
</a>
```

**Location:** Common delete modal already exists in `resources/views/common/footer.blade.php`

### **2. Migration Strategy**
**❌ WRONG:** Running `migrate:refresh` or `migrate:reset` in production/development
```bash
# DON'T DO THIS - Destroys data
php artisan migrate:refresh --seed
php artisan migrate:reset
```

**✅ CORRECT:** Run only newly created migrations
```bash
# ALWAYS DO THIS - Preserves existing data
php artisan migrate --path=database/migrations/YYYY_MM_DD_HHMMSS_specific_migration.php
```

### **3. Before Creating New Components - CHECK EXISTING STRUCTURE**

#### **Common Components to Reuse:**
1. **Delete Confirmation:** `common/footer.blade.php` (modal + JS functions)
2. **Form Validation:** Check existing `*Request.php` files for patterns
3. **AJAX Patterns:** Check `layouts/app.blade.php` for existing functions
4. **Table Actions:** Copy button patterns from existing index views
5. **Status Toggles:** Reuse existing status update patterns

#### **File Locations to Check:**
- `resources/views/common/` - Shared components
- `resources/views/layouts/app.blade.php` - Global JS functions  
- `app/Http/Requests/` - Validation patterns
- Existing controller methods for AJAX response patterns

### **4. AJAX Delete Pattern (Already Implemented)**
Controllers should return JSON for AJAX requests:
```php
// Return JSON response for AJAX requests  
if (request()->expectsJson()) {
    return response()->json([
        'status' => 'success',
        'message' => 'Item deleted successfully.'
    ]);
}

return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
```

## **Development Workflow Checklist**

### **Before Adding Any New Feature:**
- [ ] Check if similar functionality exists
- [ ] Review existing views for patterns to follow
- [ ] Check common components in `resources/views/common/`
- [ ] Review existing JS functions in `layouts/app.blade.php`
- [ ] Check existing Request classes for validation patterns
- [ ] Look at similar controllers for response patterns

### **For Database Changes:**
- [ ] Create migrations for new changes only
- [ ] Test migration on copy of database first
- [ ] Run specific migration, not migrate:refresh
- [ ] Document migration path in comments

### **For UI Components:**
- [ ] Use existing modal structures
- [ ] Follow existing button/form patterns  
- [ ] Reuse existing AJAX functions
- [ ] Maintain consistent styling with existing views

## **Key Patterns in This System:**

### **Delete Confirmation Function:**
```javascript
delete_conf_common(record_id, model, display_title, redirect_url)
```

### **Common Status Toggle:**
```php
Route::get('/update/status/{item_id}/{status}', [Controller::class, 'updateStatus'])->name('items.status');
```

### **Form Request Validation:**
```php
// For create requests
'email' => 'required|email|max:255|unique:table,email'

// For update requests  
'email' => 'required|email|max:255|unique:table,email,' . $id
```

### **Boolean Method Return Types:**
**❌ WRONG:** Direct return of nullable database field
```php
public function needsPasswordChange(): bool
{
    return $this->must_change_password; // Can return null!
}
```

**✅ CORRECT:** Explicit boolean casting for null safety
```php
public function needsPasswordChange(): bool
{
    return (bool) $this->must_change_password; // Always returns bool
}
```

**Rule:** When returning boolean from potentially null database fields, always cast with `(bool)` to handle null values properly.

---

## **⚠️ Remember: Consistency > Innovation**
Always reuse existing patterns before creating new ones. This ensures:
- Consistent user experience
- Easier maintenance  
- Reduced code duplication
- Faster development

**Last Updated:** {{ date('Y-m-d H:i:s') }}
**Created By:** Claude Code Assistant