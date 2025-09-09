# Production Deployment Checklist - Insurance Management System

## Pre-Deployment Requirements ✅

### 1. Database Migration
- [ ] **CRITICAL**: Backup production database before deployment
- [ ] Execute `database/production-migration.sql` in exact order
- [ ] Verify foreign key constraints are applied successfully
- [ ] Test database integrity with sample queries
- [ ] Monitor for any constraint violations or orphaned records

### 2. Environment Configuration
- [ ] Update `.env` file with production settings:
  ```env
  APP_ENV=production
  APP_DEBUG=false
  APP_URL=https://your-domain.com
  
  # Database Configuration
  DB_CONNECTION=mysql
  DB_HOST=your-production-host
  DB_PORT=3306
  DB_DATABASE=your_production_db
  DB_USERNAME=your_production_user
  DB_PASSWORD=your_secure_password
  
  # Cache Configuration (File-based for shared hosting)
  CACHE_DRIVER=file
  QUEUE_CONNECTION=database
  
  # Session Configuration
  SESSION_DRIVER=database
  SESSION_LIFETIME=120
  
  # Security
  SANCTUM_STATEFUL_DOMAINS=your-domain.com,api.your-domain.com
  ```

### 3. Server Requirements
- [ ] PHP 8.2+ with required extensions:
  - BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
  - GD extension (for Excel exports)
- [ ] MySQL 8.0+ or MariaDB 10.3+
- [ ] Nginx or Apache with SSL certificate
- [ ] Composer 2.0+
- [ ] Node.js 16+ and NPM (for asset compilation)

### 4. Application Setup
```bash
# 1. Install dependencies
composer install --optimize-autoloader --no-dev

# 2. Generate application key
php artisan key:generate

# 3. Run database migrations
php artisan migrate --force

# 4. Execute production SQL script
mysql -u username -p database_name < database/production-migration.sql

# 5. Create storage symlinks
php artisan storage:link

# 6. Compile assets
npm ci --production
npm run production

# 7. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Set proper permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Security Configuration ✅

### 1. Web Server Configuration
- [ ] **Nginx Configuration**:
  ```nginx
  server {
      listen 443 ssl http2;
      server_name your-domain.com;
      
      ssl_certificate /path/to/certificate.crt;
      ssl_certificate_key /path/to/private.key;
      
      root /var/www/insurance-system/public;
      index index.php;
      
      # Security headers (additional to middleware)
      add_header X-Frame-Options DENY always;
      add_header X-Content-Type-Options nosniff always;
      add_header X-XSS-Protection "1; mode=block" always;
      
      location / {
          try_files $uri $uri/ /index.php?$query_string;
      }
      
      location ~ \.php$ {
          fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
          fastcgi_index index.php;
          fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
          include fastcgi_params;
      }
  }
  ```

### 2. File Permissions
```bash
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
chmod 600 .env
```

### 3. Security Validation
- [ ] Verify SecurityHeadersMiddleware is active
- [ ] Test CSP headers are properly set
- [ ] Confirm HTTPS redirects are working
- [ ] Validate file upload restrictions
- [ ] Test rate limiting functionality

## API Configuration ✅

### 1. Sanctum Setup
- [ ] Configure SANCTUM_STATEFUL_DOMAINS in .env
- [ ] Test API authentication endpoints:
  ```bash
  # Test login
  curl -X POST https://your-domain.com/api/v1/login \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@admin.com","password":"Admin@123#"}'
  
  # Test protected endpoint
  curl -X GET https://your-domain.com/api/v1/me \
    -H "Authorization: Bearer YOUR_TOKEN"
  ```

### 2. API Rate Limiting
- [ ] Configure rate limiting in RouteServiceProvider
- [ ] Test API rate limits
- [ ] Monitor API usage and performance

## Performance Optimization ✅

### 1. Caching Setup
- [ ] File-based cache directory writable (storage/framework/cache)
- [ ] Database sessions table exists (run migration if needed)
- [ ] Test cache functionality:
  ```bash
  php artisan tinker
  Cache::put('test', 'value', 60);
  Cache::get('test'); // Should return 'value'
  
  # Test session storage
  php artisan migrate # Ensure sessions table exists
  ```

### 2. Queue Configuration
- [ ] Configure database queue table:
  ```bash
  # Create jobs table for database queue
  php artisan queue:table
  php artisan migrate
  ```
  
- [ ] Configure queue worker (shared hosting alternative):
  ```bash
  # For shared hosting, use cron job instead of supervisor
  # Add to crontab: * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
  
  # Manual queue processing (if supervisor not available)
  php artisan queue:work database --sleep=3 --tries=3 --timeout=60
  ```

### 3. Performance Monitoring
- [ ] PerformanceMonitoringMiddleware active
- [ ] Log rotation configured for performance logs
- [ ] Monitoring alerts set up for slow requests (>1s)

## Backup Strategy ✅

### 1. Database Backups
```bash
# Daily backup script
#!/bin/bash
BACKUP_DIR="/var/backups/insurance-db"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p database_name > $BACKUP_DIR/backup_$DATE.sql
gzip $BACKUP_DIR/backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete
```

### 2. File Backups
- [ ] Set up automated backups for uploaded files
- [ ] Configure off-site backup storage
- [ ] Test backup restoration procedures

## Monitoring and Logging ✅

### 1. Application Logs
- [ ] Configure log rotation in `/etc/logrotate.d/insurance`
- [ ] Set up log monitoring with Log Viewer at `/webmonks-log-viewer`
- [ ] Configure error alerting for critical issues

### 2. Performance Monitoring
- [ ] Monitor slow query log in MySQL
- [ ] Set up application performance monitoring
- [ ] Configure memory and CPU usage alerts

### 3. Security Monitoring
- [ ] Monitor security event logs
- [ ] Set up failed login attempt alerts
- [ ] Configure suspicious activity detection

## Post-Deployment Verification ✅

### 1. Functional Testing
- [ ] **Admin Portal**:
  - [ ] Login with admin credentials
  - [ ] Test customer creation and management
  - [ ] Verify insurance policy creation
  - [ ] Test quotation generation
  - [ ] Check export functionality
  - [ ] Validate WhatsApp integration

- [ ] **Customer Portal**:
  - [ ] Test customer login
  - [ ] Verify policy viewing
  - [ ] Check document downloads
  - [ ] Test family group access

- [ ] **API Endpoints**:
  - [ ] Authentication flow
  - [ ] Customer CRUD operations
  - [ ] Token refresh functionality
  - [ ] Rate limiting verification

### 2. Performance Testing
- [ ] Load test with expected concurrent users
- [ ] Monitor response times under load
- [ ] Verify caching effectiveness
- [ ] Test database performance with constraints

### 3. Security Testing
- [ ] Penetration testing checklist
- [ ] SSL/TLS configuration validation
- [ ] XSS and CSRF protection verification
- [ ] File upload security testing

## Rollback Plan ✅

### 1. Database Rollback
```sql
-- If foreign key constraints cause issues, run:
-- (Use with EXTREME caution - data loss possible)

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Drop specific constraints if needed
-- ALTER TABLE table_name DROP FOREIGN KEY constraint_name;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
```

### 2. Application Rollback
- [ ] Keep previous version backup
- [ ] Document rollback procedure
- [ ] Test rollback in staging environment

## Go-Live Checklist ✅

### Final Checks Before Going Live
- [ ] All above items completed and verified
- [ ] DNS configured and propagated
- [ ] SSL certificate installed and valid
- [ ] Backup systems operational
- [ ] Monitoring systems active
- [ ] Support team notified and ready
- [ ] User training completed
- [ ] Documentation updated

### Communication Plan
- [ ] Notify stakeholders of go-live schedule
- [ ] Prepare rollback communication plan
- [ ] Document known issues and workarounds
- [ ] Establish incident response procedures

---

## Emergency Contacts

- **Technical Lead**: [Your Contact]
- **DevOps Engineer**: [Contact]
- **Database Administrator**: [Contact]
- **System Administrator**: [Contact]

---

**CRITICAL REMINDER**: Test everything in a staging environment that mirrors production before deployment!