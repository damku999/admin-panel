# TODO List - Insurance Admin Panel

## Current Development Tasks

### 🔥 Critical Issues
- [x] **Test addon covers null value fix after unchecking** - Fixed: JavaScript now uses empty strings instead of 0
- [x] **Test Total IDV auto-calculation functionality** - Fixed: Added idv-field class and proper event delegation
- [ ] Verify Select2 customer search is working properly

### 🛠️ Features In Progress
- [ ] WhatsApp integration testing and refinement
- [ ] PDF generation optimization
- [ ] Customer portal family access improvements

### 📋 Planned Features
- [ ] Advanced quotation comparison features
- [ ] Automated renewal reminder system
- [ ] Customer feedback and rating system
- [ ] Integration with more insurance company APIs
- [ ] Mobile app development planning

### 🐛 Known Issues
- [ ] Delete quotation button may have Apache DELETE method issues
- [ ] Server-side validation display consistency across all forms
- [ ] File upload validation and security enhancements

### 🔧 Technical Debt
- [ ] Refactor JavaScript calculation functions for better maintainability
- [ ] Implement proper error handling for API failures  
- [ ] Add comprehensive test coverage for quotation service
- [ ] Database performance optimization for large datasets
- [ ] Security audit and penetration testing

### 🎨 UI/UX Improvements
- [ ] Responsive design improvements for mobile devices
- [ ] Better loading states and progress indicators
- [ ] Enhanced form validation feedback
- [ ] Dark mode implementation
- [ ] Accessibility compliance improvements

### 📚 Documentation
- [ ] API documentation for third-party integrations
- [ ] User manual for admin panel features
- [ ] Customer portal user guide
- [ ] Developer setup and deployment guide

---

## Completed Recent Tasks ✅

### 2025-09-04
- [x] **Fixed addon covers storing 0 instead of null when unchecked**
  - Updated JavaScript to clear addon fields with empty string instead of 0
  - Applied fix to both change handler and initialization function
  
- [x] **Fixed Total IDV auto-calculation not working**
  - Added `idv-field` class to all IDV input fields in quote-form partial
  - Implemented proper event delegation for dynamically added quote cards
  - Added comprehensive event listeners (input, change, keyup, blur)
  - Added debug logging for troubleshooting
  
- [x] **Implemented Select2 for Customer dropdown with search functionality**
  - Added Select2 CSS and JS dependencies
  - Enhanced dropdown with searchable interface
  - Auto-population of WhatsApp number from selected customer
  - Custom formatting showing customer name and mobile number

- [x] **Added Mark as Recommended feature with note field**
  - Created database migration for `recommendation_note` field
  - Added recommendation checkbox to both static and dynamic quote forms
  - Implemented JavaScript to show/hide note field when checkbox is ticked
  - Added validation for recommendation note (max 500 characters)
  - Updated model fillable attributes and request validation
  - Note field is required when recommendation is checked

- [x] **Added smart ordering system to Addon Covers module**
  - Created database migration for `order_no` field in `addon_covers` table
  - Added database index for performance optimization
  - Updated AddonCover model with fillable attribute and casting
  - **Implemented smart ordering logic**:
    - Auto-assigns next available order number when `order_no = 0`
    - Automatically shifts existing orders when duplicates are found
    - Added `reorganizeOrders()` method to fill gaps in sequence
  - Added `getOrdered()` static method for consistent ordering
  - Updated all queries to load addon covers by order_no
  - Added order_no field to add/edit forms with auto-assignment guidance
  - Updated admin index view to display order numbers with reorganize button
  - Added controller validation and reorganize endpoint
  - **Smart Features**: 0 = auto-assign, reorganize fills gaps (1,3,5 → 1,2,3)

### 2025-09-03
- [x] Fixed route error for quotation form generation
- [x] Added comprehensive server-side validation error display
- [x] Removed default values from addon cover fields
- [x] Fixed checkbox state preservation after validation failures
- [x] Changed numeric input step attributes from 0.01 to 1
- [x] Resolved plan_name database field conflict
- [x] Fixed CGST/SGST fields disappearing when clicking addon covers
- [x] Removed duplicate NCB percentage field

---

## Guidelines for Contributing

1. **Always test changes locally before committing**
2. **Update this TODO.md when completing tasks** 
3. **Add new issues as they are discovered**
4. **Use descriptive commit messages**
5. **Follow Laravel and Vue.js best practices**
6. **Ensure proper validation and error handling**
7. **Test on multiple browsers and devices**

---

*Last Updated: 2025-09-04*