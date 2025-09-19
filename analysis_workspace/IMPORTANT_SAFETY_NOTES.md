# ⚠️ CRITICAL SAFETY NOTES - Laravel Insurance Management System

**URGENT REMINDER**: Critical project files must NEVER be moved from root directory

---

## 🚨 CRITICAL FILES - NEVER MOVE THESE

### **Core Project Files** (Must stay in root directory)
- ✅ `composer.json` ← Composer dependencies and scripts
- ✅ `package.json` ← Node.js dependencies and build scripts
- ✅ `package-lock.json` ← Locked dependency versions
- ✅ `composer.lock` ← Locked Composer dependencies
- ✅ `.env` ← Environment configuration
- ✅ `.env.example` ← Environment template
- ✅ `artisan` ← Laravel command line interface
- ✅ `webpack.mix.js` ← Asset build configuration

### **Laravel Framework Files** (Must stay in root)
- ✅ `app/` ← Application source code
- ✅ `config/` ← Configuration files
- ✅ `routes/` ← Route definitions
- ✅ `database/` ← Migrations, seeders, factories
- ✅ `resources/` ← Views, assets, lang files
- ✅ `public/` ← Web-accessible files
- ✅ `bootstrap/` ← Framework bootstrap files
- ✅ `storage/` ← File storage and caches
- ✅ `vendor/` ← Composer dependencies

---

## ✅ FILES SAFE TO ORGANIZE

### **Analysis Files** (Can be moved to analysis_workspace)
- ✅ `CODE_ANALYSIS_FINDINGS_AND_RECOMMENDATIONS.md`
- ✅ `claudedocs/` folder contents
- ✅ `*.json` analysis files (codebase_analysis.json, etc.)
- ✅ `CLAUDE.md` (moved to workspace for organization)

### **Documentation Files** (Can be organized)
- ✅ `README.md` (if exists)
- ✅ Custom documentation files
- ✅ Analysis reports and summaries

---

## 🛡️ SAFETY PROTOCOLS

### **Before Any File Operations**
1. **Verify file type**: Is it a core Laravel/Node.js file?
2. **Check dependencies**: Does the build system need this file in root?
3. **Test impact**: Will moving this break `composer install` or `npm install`?
4. **When in doubt**: DON'T MOVE IT

### **File Move Checklist**
- [ ] File is NOT `composer.json`, `package.json`, or `package-lock.json`
- [ ] File is NOT required by Laravel framework in root directory
- [ ] File is NOT referenced by build scripts or configuration
- [ ] File is custom analysis/documentation only
- [ ] Move operation tested and verified safe

### **Emergency Recovery Protocol**
If critical files are accidentally moved:
1. **Stop all operations immediately**
2. **Locate files**: `find . -name "composer.json" -o -name "package.json"`
3. **Restore to root**: `mv path/to/file .`
4. **Verify functionality**: Test `composer install` and `npm install`
5. **Document incident**: Update safety protocols

---

## ✅ INCIDENT RESOLUTION - September 19, 2025

### **What Happened**
- Accidentally moved `composer.json`, `package.json`, `package-lock.json` to `analysis_workspace/raw_data/`
- Used overly broad `mv *.json` command that caught project files

### **Resolution Taken**
- ✅ Immediately restored files to root directory
- ✅ Verified files are intact and functional
- ✅ Added this safety documentation
- ✅ Updated file organization protocols

### **Prevention Measures Added**
- ✅ Created explicit "NEVER MOVE" file list
- ✅ Added file move checklist protocol
- ✅ Documented emergency recovery steps
- ✅ Added pre-move verification requirements

---

## 🎯 CORRECT FILE ORGANIZATION

### **Root Directory** (Core project files)
```
C:\wamp64\www\test\admin-panel\
├── composer.json              ← NEVER MOVE
├── package.json               ← NEVER MOVE
├── package-lock.json          ← NEVER MOVE
├── composer.lock              ← NEVER MOVE
├── artisan                    ← NEVER MOVE
├── webpack.mix.js             ← NEVER MOVE
├── .env                       ← NEVER MOVE
├── app/                       ← NEVER MOVE
├── config/                    ← NEVER MOVE
└── analysis_workspace/        ← Analysis files only
```

### **Analysis Workspace** (Analysis files only)
```
analysis_workspace/
├── MASTER_SESSION_CONTEXT.md
├── reports/
├── detailed_inventories/
├── raw_data/                  ← Analysis JSON files only
└── implementation_notes/
```

---

## 🔍 VERIFICATION COMMANDS

### **Verify Core Files in Root**
```bash
# Check critical files are in root
ls -la composer.json package.json package-lock.json

# Test Composer functionality
composer validate

# Test Node.js functionality
npm list --depth=0
```

### **Verify Laravel Installation**
```bash
# Test Laravel commands work
php artisan --version

# Test asset compilation
npm run dev
```

---

## 📝 LESSONS LEARNED

### **Safe File Operations**
- Always use explicit file paths instead of wildcards near core files
- Example: `mv analysis_file.json analysis_workspace/` NOT `mv *.json analysis_workspace/`
- Verify file types before bulk operations
- Test functionality after any file organization changes

### **Project Structure Respect**
- Laravel and Node.js projects have specific file location requirements
- Framework conventions must be respected for functionality
- Custom analysis files can be organized, core project files cannot
- When organizing, preserve all original project structure

---

*This safety documentation prevents critical file movement errors and ensures project integrity during analysis workspace organization.*